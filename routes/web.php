<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::get('rates', [App\Http\Controllers\RateController::class, 'index']);
Route::get('getrate', [App\Http\Controllers\RateController::class, 'storeKorona']);

Route::get('telegram-check', [App\Http\Controllers\TelegramUserController::class, 'check']);
Route::get('telegram-users', [App\Http\Controllers\TelegramUserController::class, 'index']);