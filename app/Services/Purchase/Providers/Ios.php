<?php


namespace App\Services\Purchase\Providers;


use App\Services\Purchase\PurchaseServiceAbstract;

class Ios extends PurchaseServiceAbstract
{

    public function check($receipt)
    {
        return parent::check($receipt);
    }
}
