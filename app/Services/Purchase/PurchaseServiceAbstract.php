<?php


namespace App\Services\Purchase;


abstract class PurchaseServiceAbstract implements PurchaseServiceInterface
{
    public function check($receipt)
    {
        return ($receipt % 2) === 1;
    }
}
