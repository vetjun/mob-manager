<?php


namespace App\Services\Purchase;


use App\Responses\PurchaseResponse;
use GuzzleHttp\Psr7\Request;
use Http\Mock\Client;

abstract class PurchaseServiceAbstract implements PurchaseServiceInterface
{
    public $credential = [];
    public function check($receipt): PurchaseResponse
    {
        // Mock Client
        $client = new Client();

        $username = $this->credential['username'] ?? '';
        $password = $this->credential['password'] ?? '';
        $credentials = base64_encode($username . ':' . $password);
        $firstRequest = new Request('POST', 'mock.api',
            [
                'Authorization' => 'Basic ' . $credentials,
            ], json_encode(['receipt' => $receipt]));
        $client->sendRequest($firstRequest);

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
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }
}
