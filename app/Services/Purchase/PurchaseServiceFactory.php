<?php


namespace App\Services\Purchase;


use App\Exceptions\PurchaseServiceProviderNotFound;

class PurchaseServiceFactory
{
    /**
     * @param $provider
     * @return PurchaseServiceInterface
     * @throws PurchaseServiceProviderNotFound
     */
    public static function get($provider)
    {
        $providerClassName = 'App\\Services\\Purchase\\Providers\\' . ucfirst($provider);
        if (!class_exists($providerClassName)) {
            throw new PurchaseServiceProviderNotFound('Service Provider Not Found To ' . $provider);
        }
        return new $providerClassName();
    }
}
