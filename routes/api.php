<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;

Route::group(['prefix' => 'account'], function () {
    Route::post('/', [AccountController::class, 'create']);
    Route::get('/{id}', [AccountController::class, 'show']);
    Route::put('/{id}', [AccountController::class, 'update']);
});
