<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\TransactionController;

Route::group(['prefix' => 'account'], function () {
    Route::post('/', [AccountController::class, 'create']);
    Route::get('/{id}', [AccountController::class, 'show']);
});

Route::group(['prefix' => 'transaction'], function () {
    Route::post('/', [TransactionController::class, 'create']);
});
