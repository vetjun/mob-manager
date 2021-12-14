<?php

namespace App\Console\Commands;

use App\Helper\QueueManager;
use App\Models\Repositories\SubscriptionRepository;
use Illuminate\Console\Command;

class RenewSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:renew {--loadBalanceQueueSize=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command renews expired active subscriptions';

    private $loadBalanceQueueSize = 1;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->loadBalanceQueueSize = $this->option('loadBalanceQueueSize') ?? 1;
        $toRenewLazyRecordIds = (new SubscriptionRepository())->getToRenewLazy();
        try {
            foreach ($toRenewLazyRecordIds as $index => $renewLazyRecordIds) {
                $queueName = ($this->loadBalanceQueueSize > 1) ? QueueManager::RENEW_SUBSCRIPTION . '_' . ($index % $this->loadBalanceQueueSize) : QueueManager::RENEW_SUBSCRIPTION . '_0';
                $recordIds = array_column($renewLazyRecordIds, 'id');
                $renewSubscription = new \App\Jobs\RenewSubscriptions($recordIds);
                \App\Jobs\RenewSubscriptions::dispatch($renewSubscription)->onQueue($queueName);
            }
        } catch (\Exception $exception) {
            logger()->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
