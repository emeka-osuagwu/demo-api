<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| devops
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => '/'], function () {
    Route::get('devops', [TestController::class, 'index']);
});