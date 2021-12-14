<?php


namespace App\Models\Repositories;


use App\Exceptions\AccountNotFound;
use App\Helper\ClientTokenManager;
use App\Models\Account;

class AccountRepository
{

    public function insert(array $params)
    {
        $accountObj = Account::query()
            ->where('device_uid', $params['device_uid'])
            ->where('app_id', $params['app_id'])
            ->first();
        if (empty($accountObj)) {
            $clientToken = (new ClientTokenManager())->generate(json_encode($params));
            $accountObj = new Account();
            $accountObj->setAttribute('device_uid', $params['device_uid']);
            $accountObj->setAttribute('app_id', $params['app_id']);
            $accountObj->setAttribute('operation_system', $params['operation_system']);
            $accountObj->setAttribute('language', $params['language'] ?? 'EN');
            $accountObj->setAttribute('client_token', $clientToken);
            $accountObj->save();
            $accountObj->setAttribute('status', 'inserted');
        } else {
            $accountObj->setAttribute('operation_system', $params['operation_system']);
            $accountObj->setAttribute('language', $params['language'] ?? 'EN');
            if ($accountObj->isDirty()) {
                $accountObj->save();
            }
            $accountObj->setAttribute('status', 'updated');
        }
        return $accountObj;
    }

    /**
     * @param $client_token
     * @throws AccountNotFound
     */
    public function getByClientToken($client_token)
    {
        $accountObj = Account::query()
            ->where('client_token', $client_token)
            ->first();
        if (empty($accountObj)) {
            throw new AccountNotFound('Account not found by client_token');
        }
        return $accountObj;
    }
}
