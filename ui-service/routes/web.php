<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('welcome');
});


   Route::get('login', function(){
     return view('auth.login');
 })->middleware('check.jwt');

  Route::get('/dashboard', function () { 
      return view('dashboard.index');
  })->middleware('require.jwt');

    
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