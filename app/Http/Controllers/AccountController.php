<?php


namespace App\Http\Controllers;


use App\Models\Account;
use App\Models\Repositories\AccountRepository;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class AccountController
{
    use ValidationTrait;

    public function register(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();
            $params = [
                'device_uid' => $all['device_uid'],
                'app_id' => $all['app_id'],
                'language' => $all['language'] ?? 'EN',
                'operation_system' => $all['operation_system'],
            ];
            /** @var Account $account */
            $account = (new AccountRepository())->insert($params);
            return response()->json([
                'success' => true,
                'status' => $account->getAttribute('status') ?? null,
                'client_token' => $account->getAttribute('client_token') ?? null,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }

    public function getValidationRules()
    {
        return [
            'device_uid' => 'required',
            'app_id' => 'required',
            'operation_system' => 'required',
            'language' => 'nullable',
        ];
    }
}
