<?php


namespace App\Models\Repositories;

use App\Models\Credential;

class CredentialRepository
{

    public function insert(array $params)
    {
        $credentialObj = Credential::query()->firstOrNew(
            ['app_id' => $params['app_id'], 'provider' => $params['provider']],
            ['username' => $params['username'] ?? null, 'password' => $params['password'] ?? null, 'provider' => $params['provider'], 'extra' => $params['extra'] ?? null]
        );
        if (!empty($credentialObj->getAttribute('id'))) {
            $credentialObj->setAttribute('status', 'updated');
        } else {
            $credentialObj->save();
            $credentialObj->setAttribute('status', 'inserted');
        }
        return $credentialObj;
    }

    public function getByAppIdAndProvider($appId, $provider)
    {
        $credential = Credential::query()
            ->where('app_id', $appId)
            ->where('provider', $provider)
            ->first();
        return $credential;
    }
}
