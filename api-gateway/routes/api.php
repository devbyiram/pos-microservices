<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', function (Request $request) {
    Log::info('Incoming login data:', $request->only('email', 'password'));
    $response = Http::post('http://127.0.0.1:8001/api/login', $request->only('email', 'password'));
    Log::info('Auth service response:', [$response->body()]);

    return response()->json($response->json(), $response->status());
});

Route::middleware(['verify.jwt'])->group(function () {

    Route::get('/users', function (Request $request) {
        $response = Http::internal()->get('http://127.0.0.1:8002/api/users');
        return response()->json($response->json(), $response->status());
    });

    Route::post('/users', function (Request $request) {
        $response = Http::internal()->post('http://127.0.0.1:8002/api/users', $request->all());
        return response()->json($response->json(), $response->status());
    });

    Route::get('/users/{id}', function ($id) {
        $response = Http::internal()->get("http://127.0.0.1:8002/api/users/$id");
        return response()->json($response->json(), $response->status());
    });

    Route::put('/users/{id}', function (Request $request, $id) {
        $response = Http::internal()->put("http://127.0.0.1:8002/api/users/$id", $request->all());
        return response()->json($response->json(), $response->status());
    });

    Route::delete('/users/{id}', function ($id) {
        $response = Http::internal()->delete("http://127.0.0.1:8002/api/users/$id");
        return response()->json($response->json(), $response->status());
    });

    // List all stores
    Route::get('/stores', function () {
        $response = Http::internal()->get('http://127.0.0.1:8002/api/stores');
        return response()->json($response->json(), $response->status());
    });

    // Create a new store
    Route::post('/stores', function (Request $request) {
        $response = Http::internal()->post('http://127.0.0.1:8002/api/stores', $request->all());
        return response()->json($response->json(), $response->status());
    });

    // Show single store
    Route::get('/stores/{id}', function ($id) {
        $response = Http::internal()->get("http://127.0.0.1:8002/api/stores/{$id}");
        return response()->json($response->json(), $response->status());
    });

    // Update store
    Route::put('/stores/{id}', function (Request $request, $id) {
        $response = Http::internal()->put("http://127.0.0.1:8002/api/stores/{$id}", $request->all());
        return response()->json($response->json(), $response->status());
    });

    // Delete store
    Route::delete('/stores/{id}', function ($id) {
        $response = Http::internal()->delete("http://127.0.0.1:8002/api/stores/{$id}");
        return response()->json($response->json(), $response->status());
    });
});
