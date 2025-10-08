<?php

use Illuminate\Support\Facades\Route;
use Modules\Subscription\Http\Controllers\Api\V1\SubscriptionController;
use Modules\Subscription\Http\Controllers\Api\V1\CreditWalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Customer Subscription Routes (auth:api for customers)
Route::group(['prefix' => 'subscription/v2', 'as' => 'subscription.customer.', 'middleware' => ['localization', 'auth:api']], function () {
    Route::get('/packages', [SubscriptionController::class, 'index']);
    Route::post('/packages', [SubscriptionController::class, 'store']);
    Route::get('/list', [SubscriptionController::class, 'list']);
    Route::get('/latest', [SubscriptionController::class, 'latest']);
});

Route::group(['prefix' => 'credit-wallet', 'as' => 'wallet.customer.', 'middleware' => ['localization', 'auth:api']], function () {
    Route::get('/', [CreditWalletController::class, 'get_wallet']);
    Route::get('/transactions', [CreditWalletController::class, 'get_transactions']);
});

// Driver Subscription Routes (auth:dm-api for delivery men)
Route::group(['prefix' => 'delivery-man/subscription/v2', 'as' => 'subscription.driver.', 'middleware' => ['localization', 'auth:dm-api']], function () {
    Route::get('/packages', [SubscriptionController::class, 'index']);
    Route::post('/packages', [SubscriptionController::class, 'store']);
    Route::get('/list', [SubscriptionController::class, 'list']);
    Route::get('/latest', [SubscriptionController::class, 'latest']);
});

Route::group(['prefix' => 'delivery-man/credit-wallet', 'as' => 'wallet.driver.', 'middleware' => ['localization', 'auth:dm-api']], function () {
    Route::get('/', [CreditWalletController::class, 'get_wallet']);
    Route::get('/transactions', [CreditWalletController::class, 'get_transactions']);
});

// Vendor Subscription Routes (auth:vendor-api for vendors)
Route::group(['prefix' => 'vendor/subscription/v2', 'as' => 'subscription.vendor.', 'middleware' => ['localization', 'auth:vendor-api']], function () {
    Route::get('/packages', [SubscriptionController::class, 'index']);
    Route::post('/packages', [SubscriptionController::class, 'store']);
    Route::get('/list', [SubscriptionController::class, 'list']);
    Route::get('/latest', [SubscriptionController::class, 'latest']);
});

Route::group(['prefix' => 'vendor/credit-wallet', 'as' => 'wallet.vendor.', 'middleware' => ['localization', 'auth:vendor-api']], function () {
    Route::get('/', [CreditWalletController::class, 'get_wallet']);
    Route::get('/transactions', [CreditWalletController::class, 'get_transactions']);
});
