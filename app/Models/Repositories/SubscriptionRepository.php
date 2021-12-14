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
    public function upInsert($account_id, $receipt)
    {
        $expireDate = $this->getExpireDate();
        $subscription = Subscription::query()->firstOrCreate(
            ['account_id' => $account_id],
            [
                'receipt' => $receipt,
                'expire_date' => $expireDate,
                'is_canceled' => false
            ]
        );

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
        $tz = new DateTimeZone(config('mob.timezone'));
        $oneYearFromToday->setTimeZone($tz);
        return $oneYearFromToday->format('Y-m-d H:i:s');
    }

    public function getByAccount(Account $account)
    {
        $subscription = Subscription::query()
            ->where('account_id', $account->getAttribute('id'))
            ->where('is_canceled', false)
            ->first();

        return (!empty($subscription)) ? $subscription->toArray() : [];
    }

    public function cancelByAccount(Account $account)
    {
        Subscription::query()
            ->where('account_id', $account->getAttribute('id'))
            ->where('is_canceled', false)
            ->update([
                'is_canceled' => true
            ]);
    }

    public function getToRenew()
    {
        $subscriptions =  Subscription::query()
            ->whereRaw('expire_date >= now()')
            ->where('is_canceled', false)
            ->get();

        return (!empty($subscriptions)) ? $subscriptions->toArray() : [];
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
                ->get();
            $results = (!empty($results)) ? $results->toArray() : [];
            if (empty($results)) {
                break;
            }
            yield $results;
            $page++;
        }

    }

    public function getByIds(array $subscriptionIds)
    {
        $subscription = Subscription::query()
            ->whereIn('id', $subscriptionIds)
            ->get();

        return (!empty($subscription)) ? $subscription->toArray() : [];
    }

    public function getRenewPayloadByIds(array $subscriptionIds)
    {
        $subscriptionPayload = Subscription::query()
            ->select(['subscriptions.*', 'accounts.operation_system'])
            ->whereIn('id', $subscriptionIds)
            ->join('accounts', 'accounts.id', '=', 'subscriptions.account_id')
            ->get();

        return (!empty($subscriptionPayload)) ? $subscriptionPayload->toArray() : [];
    }
}
