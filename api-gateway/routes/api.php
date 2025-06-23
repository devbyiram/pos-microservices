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
    $multipart = [];

    // Forward all simple fields except files and variants
    foreach ($request->except(['images', 'variants']) as $key => $value) {
        $multipart[] = [
            'name'     => $key,
            'contents' => $value
        ];
    }

    // Handle variants properly
    if ($request->has('variants')) {
        foreach ($request->input('variants') as $i => $variant) {
            foreach ($variant as $vKey => $vValue) {
                $multipart[] = [
                    'name'     => "variants[$i][$vKey]",
                    'contents' => $vValue
                ];
            }
        }
    }

    // Handle image file uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            $multipart[] = [
                'name'     => "images[$index]",
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName(),
                'headers'  => [
                    'Content-Type' => $file->getMimeType()
                ]
            ];
        }
    }

    // Send to Product Microservice
    $response = Http::internal()
        ->withHeaders(['Accept' => 'application/json'])
        ->asMultipart()
        ->post('http://127.0.0.1:8004/api/products', $multipart);

    return response()->json($response->json(), $response->status());
});
//-----------------------------------------------------------------------------

Route::match(['POST', 'PUT'], '/products/{id}', function (Request $request, $id) {
    $multipart = [];

    // Add regular fields except images, existing_images, and variants
    foreach ($request->except(['images', 'existing_images', 'variants']) as $key => $value) {
        $multipart[] = [
            'name'     => $key,
            'contents' => $value,
        ];
    }

    // ✅ Forward variants as nested fields
    if ($request->has('variants')) {
        foreach ($request->input('variants') as $i => $variant) {
            foreach ($variant as $vKey => $vValue) {
                $multipart[] = [
                    'name'     => "variants[$i][$vKey]",
                    'contents' => $vValue,
                ];
            }
        }
    }

    // ✅ Forward existing image IDs
    if ($request->has('existing_images')) {
        foreach ($request->input('existing_images') as $imageId) {
            $multipart[] = [
                'name'     => 'existing_images[]',
                'contents' => $imageId,
            ];
        }
    }

    // ✅ Forward uploaded image files
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
            if (!$file->isValid()) continue;
            $multipart[] = [
                'name'     => "images[$index]",
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName(),
                'headers'  => [
                    'Content-Type' => $file->getMimeType()
                ]
            ];
        }
    }

    // ✅ Add method override for PUT
    $multipart[] = [
        'name'     => '_method',
        'contents' => 'PUT',
    ];

    $response = Http::internal()
        ->asMultipart()
        ->post("http://127.0.0.1:8004/api/products/{$id}", $multipart);

    return response()->json($response->json(), $response->status());
});

// Route::match(['POST', 'PUT'], '/products/{id}', function (Request $request, $id) {
//     $multipart = [];

//     // Add regular fields except images and existing_images
//     foreach ($request->except(['images', 'existing_images']) as $key => $value) {
//         $multipart[] = [
//             'name' => $key,
//             'contents' => $value,
//         ];
//     }

//     // ✅ Forward existing image IDs (important!)
//     if ($request->has('existing_images')) {
//         foreach ($request->input('existing_images') as $imageId) {
//             $multipart[] = [
//                 'name' => 'existing_images[]',
//                 'contents' => $imageId,
//             ];
//         }
//     }

//     // ✅ Forward uploaded images
//     if ($request->hasFile('images')) {
//         foreach ($request->file('images') as $file) {
//             if (!$file->isValid()) continue;
//             $multipart[] = [
//                 'name' => 'images[]',
//                 'contents' => fopen($file->getPathname(), 'r'),
//                 'filename' => $file->getClientOriginalName(),
//             ];
//         }
//     }

//     // Add method override for PUT
//     $multipart[] = [
//         'name' => '_method',
//         'contents' => 'PUT',
//     ];

//     $response = Http::internal()
//         ->asMultipart()
//         ->post("http://127.0.0.1:8004/api/products/{$id}", $multipart);

//     return response()->json($response->json(), $response->status());
// });

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


Route::get('/variant-attributes', function () {
    $response = Http::internal()->get('http://127.0.0.1:8004/api/variant-attributes');
    return response()->json($response->json(), $response->status());
});

// Get a single variant attribute
Route::get('/variant-attributes/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/variant-attributes/{$id}");
    return response()->json($response->json(), $response->status());
});

// Create a new variant attribute
Route::post('/variant-attributes', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8004/api/variant-attributes', $request->all());
    return response()->json($response->json(), $response->status());
});

// Update an existing variant attribute
Route::put('/variant-attributes/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/variant-attributes/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete a variant attribute
Route::delete('/variant-attributes/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/variant-attributes/{$id}");
    return response()->json($response->json(), $response->status());
});


// Get all product variants
Route::get('/product-variants', function () {
    $response = Http::internal()->get('http://127.0.0.1:8004/api/product-variants');
    return response()->json($response->json(), $response->status());
});

// Get a specific product variant
Route::get('/product-variants/{id}', function ($id) {
    $response = Http::internal()->get("http://127.0.0.1:8004/api/product-variants/{$id}");
    return response()->json($response->json(), $response->status());
});

// Store new product variant
Route::post('/product-variants', function (Request $request) {
    $response = Http::internal()->post('http://127.0.0.1:8004/api/product-variants', $request->all());
    return response()->json($response->json(), $response->status());
});

// Update product variant
Route::put('/product-variants/{id}', function (Request $request, $id) {
    $response = Http::internal()->put("http://127.0.0.1:8004/api/product-variants/{$id}", $request->all());
    return response()->json($response->json(), $response->status());
});

// Delete product variant
Route::delete('/product-variants/{id}', function ($id) {
    $response = Http::internal()->delete("http://127.0.0.1:8004/api/product-variants/{$id}");
    return response()->json($response->json(), $response->status());
});
// });
