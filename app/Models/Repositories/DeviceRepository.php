<?php


namespace App\Models\Repositories;


use App\Exceptions\DeviceNotFound;
use App\Helper\ClientTokenManager;
use App\Models\Device;

class DeviceRepository
{

    public function insert(array $params)
    {
        $deviceObj = Device::query()
            ->where('device_uid', $params['device_uid'])
            ->where('app_id', $params['app_id'])
            ->first();
        if (empty($deviceObj)) {
            $clientToken = (new ClientTokenManager())->generate(json_encode($params));
            $deviceObj = new Device();
            $deviceObj->setAttribute('device_uid', $params['device_uid']);
            $deviceObj->setAttribute('app_id', $params['app_id']);
            $deviceObj->setAttribute('operation_system', $params['operation_system']);
            $deviceObj->setAttribute('language', $params['language'] ?? 'EN');
            $deviceObj->setAttribute('client_token', $clientToken);
            $deviceObj->save();
            $deviceObj->setAttribute('status', 'inserted');
        } else {
            $deviceObj->setAttribute('operation_system', $params['operation_system']);
            $deviceObj->setAttribute('language', $params['language'] ?? 'EN');
            if ($deviceObj->isDirty()) {
                $deviceObj->save();
            }
            $deviceObj->setAttribute('status', 'updated');
        }
        return $deviceObj;
    }

    /**
     * @param $client_token
     * @throws DeviceNotFound
     */
    public function getByClientToken($client_token)
    {
        $deviceObj = Device::query()
            ->where('client_token', $client_token)
            ->first();
        if (empty($deviceObj)) {
            throw new DeviceNotFound('Device not found by client_token');
        }
        return $deviceObj;
    }

    public function getDeviceAppsByClientToken($client_token)
    {
        $device = $this->getByClientToken($client_token);
        $devices = Device::query()->where('device_uid', $device->getAttribute('device_uid'))
            ->get()->toArray();

        return $devices;
    }
}
