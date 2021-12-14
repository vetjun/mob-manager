<?php


namespace App\Services\Purchase;


use App\Exceptions\CredentialNotFound;
use App\Exceptions\PurchaseServiceProviderNotFound;
use App\Models\Repositories\CredentialRepository;

class PurchaseServiceFactory
{
    private static $providerObjs = [];
    /**
     * @param $provider
     * @return PurchaseServiceInterface
     * @throws PurchaseServiceProviderNotFound|CredentialNotFound
     */
    public static function get($provider, $appId = null)
    {
        $appId = $appId ?? 'undefined';
        $providerClassName = 'App\\Services\\Purchase\\Providers\\' . ucfirst($provider);
        if (!empty(self::$providerObjs[$providerClassName][$appId])) {
            return self::$providerObjs[$providerClassName][$appId];
        }
        if (!class_exists($providerClassName)) {
            throw new PurchaseServiceProviderNotFound('Service Provider Not Found To ' . $provider);
        }
        /** @var PurchaseServiceInterface $providerObj */
        $providerObj = new $providerClassName();
        $credential = (new CredentialRepository())->getByAppIdAndProvider($appId, $provider);
        if (empty($credential)) {
            throw new CredentialNotFound('Credential Not Found To ' . $provider . ' With App -> ' . $appId);
        }
        $providerObj->setCredential($credential->toArray());
        self::$providerObjs[$providerClassName][$appId] = $providerObj;

        return self::$providerObjs[$providerClassName][$appId];
    }
}
