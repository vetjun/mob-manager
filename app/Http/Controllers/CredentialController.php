<?php


namespace App\Http\Controllers;


use App\Exceptions\ApplicationNotFound;
use App\Models\Credential;
use App\Models\Repositories\ApplicationRepository;
use App\Models\Repositories\CredentialRepository;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class CredentialController
{
    use ValidationTrait;

    public function create(Request $request)
    {
        try {
            $this->validateRequest($request);
            $all = $request->all();
            $params = [
                'app_id' => $all['app_id'],
                'username' => $all['username'] ?? null,
                'password' => $all['password'] ?? null,
                'provider' => $all['provider'] ?? null,
                'extra' => $all['extra'] ?? null
            ];
            $application = (new ApplicationRepository())->getByAppId($params['app_id']);
            if (empty($application)) {
                throw new ApplicationNotFound('Application Not Found With ' . $params['app_id']);
            }
            /** @var Credential $credential */
            $credential = (new CredentialRepository())->insert($params);
            return response()->json([
                'success' => true,
                'status' => $credential->getAttribute('status') ?? null
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }

    }

    public function getValidationRules(): array
    {
        return [
            'app_id' => 'required',
            'username' => 'nullable',
            'password' => 'nullable',
            'provider' => 'required',
            'extra' => 'nullable',
        ];
    }
}
