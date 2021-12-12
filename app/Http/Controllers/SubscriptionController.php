<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Repositories\DeviceRepository;
use App\Models\Repositories\PurchaseRepository;
use App\Services\Purchase\PurchaseServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function get(Request $request)
    {
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'client_token' => 'required'
            ]
        );
        if ($validation->fails()) {
            return [
                'success' => false,
                'message' => implode('||', $validation->getMessageBag()->all())
            ];
        }
        try {
            /** @var Device[] $devices */
            $devices = (new DeviceRepository())->getDeviceAppsByClientToken($all['client_token']);

            $purchases = (new PurchaseRepository())->getByDevices($devices);
            return [
                'status' => true,
                'subscriptions' => (array)$purchases
            ];
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }
    }
}
