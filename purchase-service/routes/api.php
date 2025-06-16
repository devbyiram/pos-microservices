<?php

use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;


Route::middleware('verify.internal.secret')->group(function () {
    Route::apiResource('purchases', PurchaseController::class);
});
