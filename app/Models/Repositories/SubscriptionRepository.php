<?php


namespace App\Models\Repositories;


use App\Models\Account;
use App\Models\Subscription;
use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository
{
    private $limitToRenewLazy = 1000;
    public function upInsert(string $account_id, $receipt)
    {
        $expireDate = $this->getExpireDate();
        $subscription = Subscription::query()->firstOrCreate([
            ['account_id' => $account_id],
            [
                'account_id' => $account_id,
                'receipt' => $receipt,
                'expire_date' => $expireDate,
                'is_canceled' => false
            ]
        ]);

        return $subscription;
    }

    public function bulkUpInsert(array $params)
    {
        $expireDate = $this->getExpireDate();
        DB::beginTransaction();
        foreach ($params as $param) {
            $accountId = $param['account_id'];
            $receipt = $param['receipt'];
            Subscription::query()->updateOrInsert([
                ['account_id' => $accountId],
                [
                    'account_id' => $accountId,
                    'receipt' => $receipt,
                    'expire_date' => $expireDate,
                    'is_canceled' => false
                ]
            ]);
        }
        DB::commit();
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

    public function getToRenew()
    {
        return Subscription::query()
            ->whereRaw('expire_date >= now()')
            ->where('is_canceled', false)
            ->get()->toArray();
    }

    public function getToRenewLazy(): \Generator
    {
        $page = 0;
        $results = [1];
        while (!empty($results)) {
            $results = Subscription::query()
                ->select(['id'])
                ->whereRaw('expire_date >= now()')
                ->where('is_canceled', false)
                ->take($this->limitToRenewLazy)->skip($page * $this->limitToRenewLazy)
                ->get()->toArray();
            if (empty($results)) {
                break;
            }
            yield $results;
            $page++;
        }

    }

    public function getByIds(array $subscriptionIds)
    {
        return Subscription::query()
            ->whereIn('id', $subscriptionIds)
            ->get()->toArray();
    }

    public function getRenewPayloadByIds(array $subscriptionIds)
    {
        return Subscription::query()
            ->select(['subscriptions.*', 'accounts.operation_system'])
            ->whereIn('id', $subscriptionIds)
            ->join('accounts', 'accounts.id', '=', 'subscriptions.account_id')
            ->get()->toArray();
    }
}
