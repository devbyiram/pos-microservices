<?php

use App\Http\Middleware\VerifyToken;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Login just forwards request to Auth Service
Route::post('/auth/login', function (Request $request) {
    Log::info('API Gateway received login request', $request->all());

    try {
        $response = Http::timeout(10)
            ->acceptJson()
            ->post('http://127.0.0.1:8001/api/login', $request->only(['email', 'password']));

        Log::info('Response from Auth Service', ['status' => $response->status(), 'body' => $response->body()]);

        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        Log::error('Auth Service unreachable', ['message' => $e->getMessage()]);

        return response()->json([
            'error' => 'Auth service is unreachable',
            'message' => $e->getMessage(),
        ], 500);
    }
});

Route::middleware('verify.jwt')->get('/auth/check', function (Request $request) {
    $response = Http::timeout(10)
        ->acceptJson()
        ->get('http://127.0.0.1:8001/api/dashboard');

    return response()->json($response->json(), $response->status());
});

Route::middleware(['verify.jwt'])->group(function () {

    Route::get('/users', function (Request $request) {
        $response = Http::get('http://127.0.0.1:8002/api/users');
        return response()->json($response->json(), $response->status());
    });

    Route::post('/users', function (Request $request) {
        $response = Http::post('http://127.0.0.1:8002/api/users', $request->all());
        return response()->json($response->json(), $response->status());
    });

    Route::get('/users/{id}', function ($id) {
        $response = Http::get("http://127.0.0.1:8002/api/users/$id");
        return response()->json($response->json(), $response->status());
    });

    Route::put('/users/{id}', function (Request $request, $id) {
        $response = Http::put("http://127.0.0.1:8002/api/users/$id", $request->all());
        return response()->json($response->json(), $response->status());
    });

    Route::delete('/users/{id}', function ($id) {
        $response = Http::delete("http://127.0.0.1:8002/api/users/$id");
        return response()->json($response->json(), $response->status());
    });

    // List all stores
    Route::get('/stores', function () {
        $response = Http::get('http://127.0.0.1:8002/api/stores');
        return response()->json($response->json(), $response->status());
    });

    // Create a new store
    Route::post('/stores', function (Request $request) {
        $response = Http::post('http://127.0.0.1:8002/api/stores', $request->all());
        return response()->json($response->json(), $response->status());
    });

    // Show single store
    Route::get('/stores/{id}', function ($id) {
        $response = Http::get("http://127.0.0.1:8002/api/stores/{$id}");
        return response()->json($response->json(), $response->status());
    });

    // Update store
    Route::put('/stores/{id}', function (Request $request, $id) {
        $response = Http::put("http://127.0.0.1:8002/api/stores/{$id}", $request->all());
        return response()->json($response->json(), $response->status());
    });

    // Delete store
    Route::delete('/stores/{id}', function ($id) {
        $response = Http::delete("http://127.0.0.1:8002/api/stores/{$id}");
        return response()->json($response->json(), $response->status());
    });
});
