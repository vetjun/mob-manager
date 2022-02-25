<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::any('/register', [AccountController::class, 'register']);
Route::any('/purchase', [PurchaseController::class, 'purchase']);
Route::any('/subscriptions', [SubscriptionController::class, 'get']);
Route::any('/cancel', [SubscriptionController::class, 'cancel']);
Route::any('/credential', [CredentialController::class, 'create']);
Route::any('/application', [ApplicationController::class, 'create']);
Route::any('/test', [\App\Http\Controllers\TestController::class, 'test']);
Route::any('/test/customer', [\App\Http\Controllers\TestController::class, 'getCustomers']);
Route::any('/bids', [\App\Http\Controllers\BidsController::class, 'get']);
Route::any('/bids/add', [\App\Http\Controllers\BidsController::class, 'add']);
Route::any('/bids/edit', [\App\Http\Controllers\BidsController::class, 'edit']);
Route::any('/bids/delete', [\App\Http\Controllers\BidsController::class, 'delete']);
Route::any('/login', [LoginController::class, 'login']);
Route::any('/me', [LoginController::class, 'me'])->middleware('jwt.auth');
