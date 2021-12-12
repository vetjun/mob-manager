<?php


namespace App\Http\Controllers;


use App\Models\Device;
use App\Models\Repositories\DeviceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController
{
    public function register(Request $request)
    {
        $all = $request->all();
        $validation = Validator::make($all,
            [
                'device_uid' => 'required',
                'app_id' => 'required',
                'operation_system' => 'required',
                'language' => 'nullable',
            ]
        );
        if ($validation->fails()) {
            return [
                'success' => false,
                'message' => implode('||', $validation->getMessageBag()->all())
            ];
        }

        $params = [
          'device_uid' => $all['device_uid'],
          'app_id' => $all['app_id'],
          'language' => $all['language'] ?? 'EN',
          'operation_system' => $all['operation_system'],
        ];
        try {
            /** @var Device $device */
            $device = (new DeviceRepository())->insert($params);
            return response()->json([
                'success' => true,
                'status' => $device->getAttribute('status') ?? null,
                'client_token' => $device->getAttribute('client_token') ?? null,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }
}
