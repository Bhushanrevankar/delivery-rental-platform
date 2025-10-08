<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('subscription')->group(function () {
    Route::get('/', 'Admin\\SubscriptionController@index');
});

Route::group(['middleware' => ['web', 'admin', 'current-module'], 'prefix' => 'admin/users/subscription', 'as' => 'admin.users.subscription.'], function () {
    Route::get('packages/status/{id}/{status}', 'Admin\\PackageController@status')->name('packages.status');
    Route::resource('packages', 'Admin\\PackageController')->names('packages');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['web', 'admin']], function () {
    Route::group(['prefix' => 'users/subscription', 'as' => 'users.subscription.', 'middleware' => ['current-module:users']], function () {
        Route::get('packages/status/{id}/{status}', 'Admin\\PackageController@status')->name('packages.status');
        Route::resource('packages', 'Admin\PackageController');
        Route::get('credit-rules/status/{id}/{status}', 'Admin\CreditDeductionRuleController@status')->name('credit-rules.status');
        Route::resource('credit-rules', 'Admin\CreditDeductionRuleController');
        Route::resource('list', 'Admin\SubscriptionController')->only(['index', 'destroy'])->parameters(['list' => 'subscription']);
        Route::resource('transactions', 'Admin\CreditTransactionController')->only(['index']);

        Route::group(['prefix' => 'bringfix', 'as' => 'bringfix.'], function () {
            Route::get('packages/status/{id}/{status}', 'Admin\BringfixPackageController@status')->name('packages.status');
            Route::resource('packages', 'Admin\BringfixPackageController');
            Route::resource('list', 'Admin\UserBringfixSubscriptionController')->only(['index', 'show', 'destroy']);
        });
    });
});
