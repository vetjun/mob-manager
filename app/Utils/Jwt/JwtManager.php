<?php


namespace App\Utils\Jwt;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtManager
{
    private string $secret;
    private string $alg;
    private $iss;
    private $aud;
    private int $exp;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET');
        $this->iss = env('JWT_ISS');
        $this->aud = env('JWT_AUD');
        $this->alg = env('JWT_ALG');
        $this->exp = env('JWT_EXP');
    }

    public function encode(array $params = [])
    {
        $now = Carbon::now();
        $params['iss'] = $this->iss;
        $params['aud'] = $this->aud;
        $params['iat'] = $now->getTimestamp();
        $params['nbf'] = $now->subSeconds(5)->getTimestamp();
        $params['exp'] = $now->addSeconds($this->exp)->getTimestamp();
        return JWT::encode($params, $this->secret, $this->alg);
    }

    public function decode(string $jwt)
    {
        return JWT::decode($jwt, new Key($this->secret, $this->alg));
    }
}
