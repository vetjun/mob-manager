<?php


namespace App\Http\Controllers;


use App\Models\Application;
use App\Models\Repositories\ApplicationRepository;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class ApplicationController
{
    use ValidationTrait;

    public function create(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();
            $params = [
                'app_id' => $all['app_id'],
                'description' => $all['description'] ?? 'Not Defined',
                'name' => $all['name']
            ];
            /** @var Application $application */
            $application = (new ApplicationRepository())->insert($params);
            return response()->json([
                'success' => true,
                'status' => $application->getAttribute('status') ?? null
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }

    public function getValidationRules()
    {
        return [
            'app_id' => 'required',
            'description' => 'nullable',
            'name' => 'required',
        ];
    }
}
