<?php


namespace App\Services\Purchase\Providers;


use App\Services\Purchase\PurchaseServiceAbstract;

class Google extends PurchaseServiceAbstract
{

    public function check($receipt)
    {
        return parent::check($receipt);
    }
}
