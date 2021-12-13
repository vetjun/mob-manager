<?php


namespace App\Services\Purchase;


use App\Exceptions\PurchaseServiceProviderNotFound;

class PurchaseServiceFactory
{
    private $providerObjs = [];
    /**
     * @param $provider
     * @return PurchaseServiceInterface
     * @throws PurchaseServiceProviderNotFound
     */
    public static function get($provider)
    {
        $providerClassName = 'App\\Services\\Purchase\\Providers\\' . ucfirst($provider);
        if (!empty($providerObjs[$providerClassName])) {
            return $providerObjs[$providerClassName];
        }
        if (!class_exists($providerClassName)) {
            throw new PurchaseServiceProviderNotFound('Service Provider Not Found To ' . $provider);
        }
        $providerObjs[$providerClassName] = new $providerClassName();
        return $providerObjs[$providerClassName];
    }
}
