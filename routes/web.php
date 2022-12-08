<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('rates', [App\Http\Controllers\RateController::class, 'index']);
Route::get('getrate', [App\Http\Controllers\RateController::class, 'store']);

Route::get('telegram-check', [App\Http\Controllers\TelegramUserController::class, 'check']);