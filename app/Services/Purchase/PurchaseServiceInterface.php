<?php


namespace App\Services\Purchase;


use App\Responses\PurchaseResponse;

interface PurchaseServiceInterface
{
    public function check($receipt): PurchaseResponse;
    public function setCredential($credential);
}
