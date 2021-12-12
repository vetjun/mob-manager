<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Repositories\DeviceRepository;
use App\Models\Repositories\PurchaseRepository;
use App\Services\Purchase\PurchaseServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'client_token' => 'required',
                'receipt' => 'required'
            ]
        );
        if ($validation->fails()) {
            return [
                'success' => false,
                'message' => implode('||', $validation->getMessageBag()->all())
            ];
        }
        try {
            /** @var Device $device */
            $device = (new DeviceRepository())->getByClientToken($all['client_token']);

            $purchaseService = PurchaseServiceFactory::get($device->getAttribute('operation_system'));
            $checkResponse = $purchaseService->check($all['receipt']);
            $purchase = (new PurchaseRepository())->insert($all, $device->getAttribute('id'), $checkResponse);
            if ($checkResponse === true) {
                return response()->json([
                    'status' => true,
                    'expireDate' => $purchase->getAttribute('expire_date'),
                ], 200);
            }
        } catch (\Exception $exception) {
            return [
                'status' => false,
                'message' => $exception->getMessage()
            ];
        }

        return response()->json([
            'status' => false,
            'message' => 'Purchase Operation Failed',
        ], 500);
    }
}
