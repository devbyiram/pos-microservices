<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;


Route::middleware('verify.internal.secret')->group(function () {
    Route::apiResource('purchases', PurchaseController::class);
});
