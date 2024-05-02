<?php

use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| add comment
|--------------------------------------------------------------------------
*/
function hashValue($value)
{
    return Hash::make($value);
}

function hashCheck($value, $hashed_value){
    return Hash::check($value, $hashed_value);
}

function bearerToken() {
    $header = request()->header('AUTHORIZATION');
    if (Str::startsWith($header, 'Bearer ')) {
        return Str::substr($header, 7);
    }
}