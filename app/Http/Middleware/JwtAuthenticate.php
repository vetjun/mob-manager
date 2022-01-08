<?php

namespace App\Http\Middleware;

use App\Utils\Crypt\CryptManager;
use App\Utils\Jwt\JwtManager;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JwtAuthenticate
{
    /**
     * @var JwtManager
     */
    private JwtManager $jwtManager;
    /**
     * @var CryptManager
     */
    private CryptManager $cryptManager;

    public function __construct(JwtManager $jwtManager, CryptManager $cryptManager)
    {
        $this->jwtManager = $jwtManager;
        $this->cryptManager = $cryptManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');
        $jwtToken = str_replace('JWT ', '', $authorization);
        try {
            $decoded = (array) $this->jwtManager->decode($jwtToken);
            $payload = $decoded['payload'];
            $payloadStr = $this->cryptManager->decrypt($payload);
            $payloadArr = json_decode($payloadStr, true);
            $request->merge(['authData' => $payloadArr]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
