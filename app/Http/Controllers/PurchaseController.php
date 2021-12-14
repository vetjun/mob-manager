<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Repositories\AccountRepository;
use App\Models\Repositories\PurchaseRepository;
use App\Models\Repositories\SubscriptionRepository;
use App\Services\Purchase\PurchaseServiceFactory;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    use ValidationTrait;

    public function purchase(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();

            /** @var Account $account */
            $account = (new AccountRepository())->getByClientToken($all['client_token']);

            $purchaseService = PurchaseServiceFactory::get($account->getAttribute('operation_system'));
            $receipt = $all['receipt'];
            $purchaseResponse = $purchaseService->check($all['receipt']);
            (new PurchaseRepository())->insert($all, $account->getAttribute('id'), $purchaseResponse);
            if ($purchaseResponse->getStatusCode() === 200) {
                $subscription = (new SubscriptionRepository())->upInsert($account->getAttribute('id'), $receipt);
                return response()->json([
                    'status' => true,
                    'expireDate' => $subscription->getAttribute('expire_date'),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Purchase Request Could Not Been Approved',
                    'error_code' => $purchaseResponse->getErrorCode()
                ], 500);
            }
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}
