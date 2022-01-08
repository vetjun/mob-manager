<?php


namespace App\Http\Controllers;


use App\Utils\Crypt\CryptManager;
use App\Utils\Jwt\JwtManager;
use App\Utils\Traits\ValidationTrait;
use Illuminate\Http\Request;

class LoginController
{
    use ValidationTrait;

    public function login(Request $request, JwtManager $jwtManager, CryptManager $cryptManager)
    {
        try {
            $this->validateRequest($request);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }

        try {
            $all = $request->all();
            $payload = [
                'username' => $all['username']
            ];
            $payloadStr = $cryptManager->encrypt(json_encode($payload));
            $jwtToken = $jwtManager->encode(['payload' => $payloadStr]);
            return response()->json([
                'success' => true,
                'token' => $jwtToken,
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
        }

    }

    public function me(Request $request)
    {
        $all = $request->all();
        return response()->json([
            'success' => true,
            'data' => $all
        ], 200);
    }

    public function getValidationRules()
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }
}
