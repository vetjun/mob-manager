<?php


namespace App\Models\Repositories;


use App\Models\Purchase;
use DateInterval;
use DateTime;
use DateTimeZone;

class PurchaseRepository
{
    public function insert(array $params, string $device_id, bool $checkResponse)
    {
        $expireDate = $this->getExpireDate();
        $purchase = new Purchase();
        $purchase->setAttribute('request', json_encode($params));
        $purchase->setAttribute('is_success', $checkResponse);
        $purchase->setAttribute('device_id', $device_id);
        $purchase->setAttribute('expire_date', $expireDate);
        $purchase->save();
        return $purchase;
    }

    private function getExpireDate()
    {
        $today = new DateTime();
        $oneYearFromToday = $today->add(DateInterval::createFromDateString('1 years'));
        $tz = new DateTimeZone('Etc/GMT+6');
        $oneYearFromToday->setTimeZone($tz);
        return $oneYearFromToday->format('Y-m-d H:i:s');
    }

    public function getByDevices(array $devices)
    {
        $deviceIds = [];
        foreach ($devices as $device) {
            $deviceIds[] = $device['id'];
        }
        $purchases = Purchase::query()->select(['device_id', 'expire_date'])
            ->where('is_success', true)
            ->whereIn('device_id', $deviceIds)
            ->orderByDesc('expire_date')
            ->groupBy('device_id', 'expire_date')
            ->get()->toArray();
        return $purchases;
    }
}
