<?php


namespace App\Helper;


use DateTime;
use Illuminate\Support\Facades\Hash;

class ClientTokenManager
{
    public function generate($payload): string
    {
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        return Hash::make($payload . $timestamp);
    }
}
