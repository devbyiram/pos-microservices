<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/dashboard', function () {
//     return view('dashboard.index');
// })->name('dashboard');

   Route::get('login', function(){
     return view('auth.login');
 })->middleware('check.jwt');

  Route::get('/dashboard', function () {
        return view('dashboard.index');
    });
    // Route::group(['middleware' => 'guest'],function(){

        // Route::controller(LoginController::class)->group(function () {
        //     Route::get('login','index')->name(name:'login');
        //     Route::post('authenticate', 'authenticate')->name(name:'authenticate');
        // });

    // });

// Route::middleware('check.jwt')->group(function () {
  
//     Route::get('/dashboard', function () {
//         return view('dashboard.index');
//     });
// });


    

Route::post('/store-token', function (Request $request) {
    $token = $request->input('token');

    return response('Token stored')->cookie(
        'jwt_token', $token, 60*60*24 , '/', null, false, true, false, 'Strict'
    );
});