<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFound;
use App\Models\Account;
use App\Models\Repositories\AccountRepository;
use App\Models\Repositories\PurchaseRepository;
use App\Models\Repositories\SubscriptionRepository;
use App\Services\Purchase\PurchaseServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'client_token' => 'required',
                'receipt' => 'required'
            ]
        );
        if ($validation->fails()) {
            return [
                'success' => false,
                'message' => implode('||', $validation->getMessageBag()->all())
            ];
        }
        try {
            /** @var Account $account */
            $account = (new AccountRepository())->getByClientToken($all['client_token']);
            if (empty($account)) {
                throw new AccountNotFound('Account Not Found By Client Token');
            }

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
