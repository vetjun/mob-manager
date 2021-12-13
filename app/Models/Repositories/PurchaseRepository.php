<?php


namespace App\Models\Repositories;


use App\Models\Purchase;
use App\Responses\PurchaseResponse;

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
}
