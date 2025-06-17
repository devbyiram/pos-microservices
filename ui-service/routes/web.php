<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;

Route::get('/', function () {
    return view('welcome');
});


   Route::get('login', function(){
     return view('auth.login');
 })->middleware('check.jwt');

  Route::get('/dashboard', function () { 
      return view('dashboard.index');
  })->middleware('require.jwt')->name('dashboard.index');

    
Route::post('/store-token', function (Request $request) {
    $token = $request->input('token');

    return response('Token stored')->cookie(
        'jwt_token', $token, 60*60*24 , '/', null, false, true, false, 'Strict'
    );
});

    Route::get('/logout', function () {
    return redirect('/login')->with('success', 'Logged out successfully.')->cookie(
        Cookie::forget('jwt_token')
    );
})->name('logout');


Route::get('/users', function () {
    return view('dashboard.users.index');
})->middleware('require.jwt')->name('users.index');

Route::get('/users/create', function () {
    return view('dashboard.users.create');
})->middleware('require.jwt')->name('users.create');

Route::get('/users/edit/{id}', function ($id) {
    return view('dashboard.users.edit', ['user_id' => $id]);
})->middleware('require.jwt')->name('users.edit');

Route::get('/stores', function () {
    return view('dashboard.stores.index');
})->middleware('require.jwt')->name('stores.index');

Route::get('/stores/create', function () {
    return view('dashboard.stores.create');
})->middleware('require.jwt')->name('stores.create');

Route::get('/stores/edit/{id}', function ($id) {
    return view('dashboard.stores.edit', ['store_id' => $id]);
})->middleware('require.jwt')->name('stores.edit');

Route::get('/brands', function () {
    return view('dashboard.brands.index');
})->middleware('require.jwt')->name('brands.index');

Route::get('/brands/create', function () {
    return view('dashboard.brands.create');
})->middleware('require.jwt')->name('brands.create');

Route::get('/brands/edit/{id}', function ($id) {
    return view('dashboard.brands.edit', ['brand_id' => $id]);
})->middleware('require.jwt')->name('brands.edit');
  
Route::get('/categories', function () {
    return view('dashboard.categories.index');
})->middleware('require.jwt')->name('categories.index'); 
 
Route::get('/categories/create', function () {
    return view('dashboard.categories.create');
})->middleware('require.jwt')->name('categories.create');
 
Route::get('/categories/edit/{id}', function ($id) {
    return view('dashboard.categories.edit', ['category_id' => $id]);
})->middleware('require.jwt')->name('categories.edit');

Route::get('/vendors', function () {
    return view('dashboard.vendors.index');
})->middleware('require.jwt')->name('vendors.index');

Route::get('/vendors/create', function () {
    return view('dashboard.vendors.create');
})->middleware('require.jwt')->name('vendors.create');
 
Route::get('/vendors/edit/{id}', function ($id) {
    return view('dashboard.vendors.edit', ['vendor_id' => $id]);
})->middleware('require.jwt')->name('vendors.edit');    


Route::get('/products', function () {
    return view('dashboard.products.index');
})->middleware('require.jwt')->name('products.index');

Route::get('/products/create', function () {
    return view('dashboard.products.create');
})->middleware('require.jwt')->name('products.create');

Route::get('/products/edit/{id}', function ($id) {
    return view('dashboard.products.edit', ['product_id' => $id]);
})->middleware('require.jwt')->name('products.edit');

Route::get('/variant-attributes', function () {
    return view('dashboard.variant-attributes.index');
})->middleware('require.jwt')->name('variant-attributes.index');

Route::get('/variant-attributes/create', function () {
    return view('dashboard.variant-attributes.create');
})->middleware('require.jwt')->name('variant-attributes.create');  

Route::get('/variant-attributes/edit/{id}', function ($id) {
    return view('dashboard.variant-attributes.edit', ['variant_attribute_id' => $id]);
})   ->middleware('require.jwt')->name('variant-attributes.edit');

Route::get('/product-variants', function () {
    return view('dashboard.product-variants.index');
})->middleware('require.jwt')->name('product-variants.index');

Route::get('/product-variants/create', function () {
    return view('dashboard.product-variants.create');
})->middleware('require.jwt')->name('product-variants.create');

Route::get('/product-variants/edit/{id}', function ($id) {
    return view('dashboard.product-variants.edit', ['product_variant_id' => $id]);
})   ->middleware('require.jwt')->name('product-variants.edit');

Route::get('/purchases', function () {
    return view('dashboard.purchase.index');
})->middleware('require.jwt')->name('purchases.index');

Route::get('/purchases/create', function () {
    return view('dashboard.purchase.create');
})->middleware('require.jwt')->name('purchases.create');

Route::get('/purchases/edit/{id}', function ($id) {
    return view('dashboard.purchase.edit', ['purchase_id' => $id]);
})->middleware('require.jwt')->name('purchases.edit');