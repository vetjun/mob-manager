<?php


namespace App\Models\Repositories;


use App\Models\Purchase;
use App\Responses\PurchaseResponse;
use DateTime;

class PurchaseRepository
{
    public function insert(array $params, string $account_id, PurchaseResponse $purchaseResponse)
    {
        $purchase = new Purchase();
        $isSuccess = $purchaseResponse->getStatusCode() === 200;
        $purchase->setAttribute('request', json_encode($params));
        $purchase->setAttribute('response', json_encode($purchaseResponse->getData()));
        $purchase->setAttribute('error_code', $purchaseResponse->getErrorCode());
        $purchase->setAttribute('is_success', $isSuccess);
        $purchase->setAttribute('account_id', $account_id);
        $purchase->save();
        return $purchase;
    }

    public function multiInsert(array $purchaseResponses)
    {
        $purchases = [];
        $timeNow = new Datetime();
        foreach ($purchaseResponses as $purchaseResponse) {
            /** @var PurchaseResponse $response */
            $response = $purchaseResponse['response'];
            $account_id = $purchaseResponse['account_id'];
            $params = $purchaseResponse['params'] ?? [];
            $purchase = new Purchase();
            $isSuccess = $response->getStatusCode() === 200;
            $purchase->setAttribute('request', json_encode($params));
            $purchase->setAttribute('response', json_encode($response->getData()));
            $purchase->setAttribute('error_code', $response->getErrorCode());
            $purchase->setAttribute('is_success', $isSuccess);
            $purchase->setAttribute('account_id', $account_id);
            $purchase->setAttribute('created_at', $timeNow);
            $purchase->setAttribute('updated_at', $timeNow);
            $purchases[] = $purchase;
        }
        Purchase::query()->insert($purchases);
    }
}
