<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FsfpayController;

Route::get('/fsfpay/checkout', [FsfpayController::class, 'checkout']);
Route::post('/fsfpay/callback', [FsfpayController::class, 'callback']);
