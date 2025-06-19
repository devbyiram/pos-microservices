<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;


Route::middleware('verify.internal.secret')->group(function () {
    Route::apiResource('purchases', PurchaseController::class);
    // Route::apiResource('purchaseitems', PurchaseItemController::class);
});
