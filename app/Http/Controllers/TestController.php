<?php

namespace App\Http\Controllers;

use App\Utils\Crypt\CryptManager;
use App\Utils\Jwt\JwtManager;
use Illuminate\Http\Request;

class TestController
{
    public function test(Request $request, JwtManager $jwtManager, CryptManager $cryptManager){
        return response()->json([
            [
                'id' => 1,
                'name' => 'Adidas Shoe',
                'price' => 150,
            ],
            [
                'id' => 2,
                'name' => 'Puma Socks',
                'price' => 10,
            ],

        ]);
    }
}
