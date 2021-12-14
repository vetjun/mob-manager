<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::any('/register', [AccountController::class, 'register']);
Route::any('/purchase', [PurchaseController::class, 'purchase']);
Route::any('/subscriptions', [SubscriptionController::class, 'get']);
Route::any('/cancel', [SubscriptionController::class, 'cancel']);
