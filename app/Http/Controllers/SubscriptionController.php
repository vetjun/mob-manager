<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Repositories\AccountRepository;
use App\Models\Repositories\SubscriptionRepository;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use ValidationTrait;

    public function get(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();

            /** @var Account $account */
            $account = (new AccountRepository())->getByClientToken($all['client_token']);

            $subscription = (new SubscriptionRepository())->getByAccount($account);
            return [
                'status' => true,
                'subscription' => $subscription
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function cancel(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();

            /** @var Account $account */
            $account = (new AccountRepository())->getByClientToken($all['client_token']);

            (new SubscriptionRepository())->cancelByAccount($account);
            return [
                'status' => true,
                'message' => 'Successfully Canceled Subscription'
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }

    public function getValidationRules()
    {
        return [
            'client_token' => 'required'
        ];
    }
}
