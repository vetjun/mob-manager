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

    public function getCustomers()
    {
        return response()->json([
            [
                'Name' => 'aaa',
                'Surname' => 'bbb',
                'Phone' => '+905070937290',
            ],
            [
                'Name' => 'zzz',
                'Surname' => 'zzz',
                'Phone' => '+905070937290',
            ],
            [
                'Name' => 'fff',
                'Surname' => 'fff',
                'Phone' => '+905070937290',
            ],
            [
                'Name' => 'kkk',
                'Surname' => 'kkk',
                'Phone' => '+905070937290',
            ],
            [
                'Name' => 'lll',
                'Surname' => 'mmm',
                'Phone' => '+905070937290',
            ],
            [
                'Name' => 'nnn',
                'Surname' => 'nnn',
                'Phone' => '+905070937290',
            ],
        ]);
    }
}
