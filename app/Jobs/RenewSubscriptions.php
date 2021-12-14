<?php

namespace App\Jobs;

use App\Helper\QueueManager;
use App\Models\Repositories\PurchaseRepository;
use App\Models\Repositories\SubscriptionRepository;
use App\Services\Purchase\PurchaseServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $queueNameToRetry = 'renew_subscriptions_retry';
    private $asyncChunkLimit = 10;
    private $errorCodesToRetry = ['RATE_LIMIT'];
    /**
     * @var array
     */
    private $subscriptionIds = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($subscriptionIds)
    {
        $this->subscriptionIds = $subscriptionIds;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $purchaseRepository = new PurchaseRepository();
        $subscriptionRepository = new SubscriptionRepository();
        $subscriptions = $subscriptionRepository->getRenewPayloadByIds($this->subscriptionIds);
        $sizeOfRecords = count($subscriptions);
        $chunkSize = (int)($sizeOfRecords / $this->asyncChunkLimit);
        foreach (array_chunk($subscriptions, $chunkSize) as $chunkedRecords) {
            $retryToRenewSubscriptions = [];
            $purchaseResponses = [];
            $subscriptionAccountIdsToUpInsert = [];
            foreach ($chunkedRecords as $chunkedRecord) {
                try {
                    $id = $chunkedRecord['id'];
                    $appId = $chunkedRecord['app_id'];
                    $provider = $chunkedRecord['operation_system'];
                    $receipt = $chunkedRecord['receipt'];
                    $accountId = $chunkedRecord['account_id'];
                    $purchaseService = PurchaseServiceFactory::get($provider, $appId);
                    $response = $purchaseService->check($receipt);
                    $purchaseResponses[] = [
                        'response' => $response,
                        'account_id' => $accountId,
                        'params' => [
                            'receipt' => $receipt
                        ]
                    ];
                    if ($response->getStatusCode() === 200) {
                        $subscriptionAccountIdsToUpInsert[] = [
                            'id' => $id,
                            'account_id' => $accountId,
                            'receipt' => $receipt,
                        ];
                        continue;
                    }
                    if (in_array($response->getErrorCode(), $this->errorCodesToRetry)) {
                        $retryToRenewSubscriptions[] = $chunkedRecord;
                    }
                } catch (\Exception $exception) {
                    logger()->error($exception->getMessage());
                }
            }
            $subscriptionRepository->bulkUpInsert($subscriptionAccountIdsToUpInsert);
            $purchaseRepository->multiInsert($purchaseResponses);
            if (!empty($retryToRenewSubscriptions)) {
                $renewJob = new RenewSubscriptions($retryToRenewSubscriptions);
                RenewSubscriptions::dispatch($renewJob)->onQueue(QueueManager::RENEW_SUBSCRIPTION_RETRY);
            }
        }
    }
}
