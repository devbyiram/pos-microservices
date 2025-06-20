<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VariantAttributeController;

Route::middleware('verify.internal.secret')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('brands', BrandController::class);
    Route::apiResource('vendors', VendorController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('variant-attributes', VariantAttributeController::class);

});