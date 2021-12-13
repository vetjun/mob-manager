<?php


namespace App\Models\Repositories;


use App\Models\Account;
use App\Models\Subscription;
use DateInterval;
use DateTime;
use DateTimeZone;

class SubscriptionRepository
{
    public function upInsert(string $account_id)
    {
        $expireDate = $this->getExpireDate();
        $subscription = Subscription::query()->firstOrCreate([
            ['account_id' => $account_id],
            [
                'account_id' => $account_id,
                'expire_date' => $expireDate,
                'is_canceled' => false
            ]
        ]);

        return $subscription;
    }

    private function getExpireDate()
    {
        $today = new DateTime();
        $oneYearFromToday = $today->add(DateInterval::createFromDateString('1 years'));
        $tz = new DateTimeZone('Etc/GMT+6');
        $oneYearFromToday->setTimeZone($tz);
        return $oneYearFromToday->format('Y-m-d H:i:s');
    }

    public function getByAccount(Account $account)
    {
        return Subscription::query()
            ->where('account_id', $account->getAttribute('id'))
            ->where('is_canceled', false)
            ->first()->toArray();
    }

    public function cancelByAccount(Account $account)
    {
        Subscription::query()
            ->where('account_id', $account->getAttribute('id'))
            ->where('is_canceled', false)
            ->delete();
    }
}
