<?php


namespace App\Services\Purchase;


use App\Exceptions\PurchaseServiceProviderNotFound;

class PurchaseServiceFactory
{
    private static $providerObjs = [];
    /**
     * @param $provider
     * @return PurchaseServiceInterface
     * @throws PurchaseServiceProviderNotFound
     */
    public static function get($provider)
    {
        $providerClassName = 'App\\Services\\Purchase\\Providers\\' . ucfirst($provider);
        if (!empty(self::$providerObjs[$providerClassName])) {
            return self::$providerObjs[$providerClassName];
        }
        if (!class_exists($providerClassName)) {
            throw new PurchaseServiceProviderNotFound('Service Provider Not Found To ' . $provider);
        }
        self::$providerObjs[$providerClassName] = new $providerClassName();
        return self::$providerObjs[$providerClassName];
    }
}
