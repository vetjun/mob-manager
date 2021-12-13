<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFound;
use App\Models\Account;
use App\Models\Repositories\AccountRepository;
use App\Models\Repositories\SubscriptionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function get(Request $request)
    {
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'client_token' => 'required'
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
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'client_token' => 'required'
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
}
