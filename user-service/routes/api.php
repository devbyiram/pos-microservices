<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;

Route::middleware('verify.internal.secret')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);

    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::apiResource('stores', StoreController::class);
});


// Route::get('/stores', [UserController::class, 'index']);
// Route::post('/stores', [UserController::class, 'store']);
// Route::get('/stores/{id}', [UserController::class, 'show']);
// Route::put('/stores/{id}', [UserController::class, 'update']);
// Route::delete('/stores/{id}', [UserController::class, 'destroy']);
