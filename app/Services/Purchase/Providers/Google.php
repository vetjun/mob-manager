<?php


namespace App\Services\Purchase\Providers;


use App\Responses\PurchaseResponse;
use App\Services\Purchase\PurchaseServiceAbstract;

class Google extends PurchaseServiceAbstract
{

    public function check($receipt): PurchaseResponse
    {
        return parent::check($receipt);
    }
}
