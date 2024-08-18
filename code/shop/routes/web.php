<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//prefix for api
Route::prefix('api')->group(function () {
    //prefix for v1
    Route::prefix('v1')->group(function () {
        Route::prefix('offers')->group(function () {
            //get product by id
            Route::get('/{value}', 'App\Http\Controllers\OfferController@get');
        });
    });
});