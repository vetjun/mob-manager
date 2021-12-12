<?php
use Illuminate\Support\Facades\Route;

Route::any('/register', [\App\Http\Controllers\DeviceController::class, 'register']);
Route::any('/purchase', [\App\Http\Controllers\PurchaseController::class, 'purchase']);
Route::any('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'get']);
