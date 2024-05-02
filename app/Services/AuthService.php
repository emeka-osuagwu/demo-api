<?php

namespace App\Services;

use App\Exceptions\AppException;
use Illuminate\Support\Facades\Redis;
use Predis\Connection\ConnectionException;

class AuthService
{
    /*
    |--------------------------------------------------------------------------
    | Build Token
    |--------------------------------------------------------------------------
    */
    function buildToken(): string
    {
        $token = generateUUID();
        return  "T1|{$token}";
    }

    /*
    |--------------------------------------------------------------------------
    | Build Key
    |--------------------------------------------------------------------------
    */
    function buildKey(string $token): string
    {
        return  "auths:{$token}";
    }

    /*
    |--------------------------------------------------------------------------
    | Create Authentication
    |--------------------------------------------------------------------------
    */
    function createAuthentication($payload): string
    {
        /*
        |--------------------------------------------------------------------------
        | set variable
        |--------------------------------------------------------------------------
        */
        $token = $this->buildToken();
        $key = $this->buildKey($token);

        /*
        |--------------------------------------------------------------------------
        | set payload
        |--------------------------------------------------------------------------
        */
        $payload = [
            'payload' => $payload,
            'created_at' => now()->toDateTimeString()
        ];

        Redis::set($key, json_encode($payload));
        return $token;
    }

    /*
    |--------------------------------------------------------------------------
    | Check Authorization
    |--------------------------------------------------------------------------
    */
    function checkAuthorization(string $token)
    {
        return json_decode(Redis::get("auths:{$token}"), true) ?? [];
    }
}
