<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', function (Request $request) {
   $response = Http::internal()->post('http://127.0.0.1:8001/api/login', $request->only('email', 'password'));
    return response()->json($response->json(), $response->status());
});


// Route::middleware(['verify.jwt'])->group(function () {

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


  // List categories
Route::get('/categories', function () {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/categories");
    return response()->json($response->json(), $response->status());
});


// Create category
Route::post('/categories', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8004/api/categories', $request->all());
    return response()->json($response->json(), $response->status());
});

// Show category
Route::get('/categories/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/categories/{$id}");
    return response()->json($response->json(), $response->status());
});

// Update category
Route::put('/categories/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/categories/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete category
Route::delete('/categories/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/categories/{$id}");
    return response()->json($response->json(), $response->status());
});



// List brands
Route::get('/brands', function () {
    $response = Http::internal()->get('http://127.0.0.1:8004/api/brands');
    return response()->json($response->json(), $response->status());
});

// Create brand
Route::post('/brands', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8004/api/brands', $request->all());
    return response()->json($response->json(), $response->status());
});

// Show brand
Route::get('/brands/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/brands/{$id}");
    return response()->json($response->json(), $response->status());
});

// Update brand
Route::put('/brands/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/brands/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete brand
Route::delete('/brands/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/brands/{$id}");
    return response()->json($response->json(), $response->status());
});


// List all vendors
Route::get('/vendors', function () {
    $response = Http::internal()->get('http://127.0.0.1:8004/api/vendors');
    return response()->json($response->json(), $response->status());
});

// Show single vendor
Route::get('/vendors/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/vendors/{$id}");
    return response()->json($response->json(), $response->status());
});

// Create vendor
Route::post('/vendors', function (Request $request) {
    $response = Http::internal()->post("http://127.0.0.1:8004/api/vendors", $request->all());
    return response()->json($response->json(), $response->status());
});

// Update vendor
Route::put('/vendors/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/vendors/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete vendor
Route::delete('/vendors/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/vendors/{$id}");
    return response()->json($response->json(), $response->status());
});


Route::get('/products', function () {
    $response = Http::internal()->get('http://127.0.0.1:8004/api/products');
    return response()->json($response->json(), $response->status());
});

Route::get('/products/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/products/{$id}");
    return response()->json($response->json(), $response->status());
});

Route::post('/products', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8004/api/products', $request->all());
    return response()->json($response->json(), $response->status());
});

Route::put('/products/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/products/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

Route::delete('/products/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/products/{$id}");
    return response()->json($response->json(), $response->status());
});


// Get all purchases
Route::get('/purchases', function () {
    $response = Http::internal()->get('http://127.0.0.1:8005/api/purchases');
    return response()->json($response->json(), $response->status());
});

// Get a single purchase
Route::get('/purchases/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8005/api/purchases/{$id}");
    return response()->json($response->json(), $response->status());
});

// Create a new purchase
Route::post('/purchases', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8005/api/purchases', $request->all());
    return response()->json($response->json(), $response->status());
});

// Update an existing purchase
Route::put('/purchases/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8005/api/purchases/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete a purchase
Route::delete('/purchases/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8005/api/purchases/{$id}");
    return response()->json($response->json(), $response->status());
});


Route::get('/purchaseitems', function () {
    $response = Http::internal()->get('http://127.0.0.1:8005/api/purchaseitems');
    return response()->json($response->json(), $response->status());
});

Route::get('/purchaseitems/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8005/api/purchaseitems/{$id}");
    return response()->json($response->json(), $response->status());
});

Route::post('/purchaseitems', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8005/api/purchaseitems', $request->all());
    return response()->json($response->json(), $response->status());
});

Route::put('/purchaseitems/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8005/api/purchaseitems/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

Route::delete('/purchaseitems/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8005/api/purchaseitems/{$id}");
    return response()->json($response->json(), $response->status());
});
// });
