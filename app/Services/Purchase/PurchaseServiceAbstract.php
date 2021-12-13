<?php


namespace App\Services\Purchase;


use App\Responses\PurchaseResponse;

abstract class PurchaseServiceAbstract implements PurchaseServiceInterface
{
    public function check($receipt): PurchaseResponse
    {
        $response = new PurchaseResponse();
        $receiptInt = (int)$receipt;
        $remain = $receiptInt % 100;
        if (($remain % 6) === 0) {
            $response->setErrorCode('RATE_LIMIT');
            $response->setErrorMessage('RATE LIMIT SIZE EXCEED');
            $response->setStatusCode(500);
            $response->setData([
                'status' => false,
                'message' => $response->getErrorMessage()
            ]);
            return $response;
        }
        if (($receipt % 2) !== 1) {
            $response->setErrorCode('NOT_APPROVED');
            $response->setErrorMessage('Purchase Request Not Approved');
            $response->setStatusCode(500);
            $response->setData([
                'status' => false,
                'message' => $response->getErrorMessage()
            ]);
            return $response;
        }
        $response->setStatusCode(200);
        $response->setData([
            'status' => true,
            'message' => 'Successfully Has Been Approved'
        ]);
        return $response;
    }
}
