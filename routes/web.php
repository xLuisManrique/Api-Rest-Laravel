<?php

use Illuminate\Support\Facades\Route;

// Charge Class
use App\Http\Middleware\ApiAuthMiddleware;

// ROUTE OF TESTING
Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '.$nombre;
    
    return view('pruebas', array(
        'texto' => $texto
    ));
});
Route::get('testing', 'App\Http\Controllers\PruebasController@testOrm');

// ROUTE OF API

    // ROUTE API TESTING
    Route::get('testingUser', 'App\Http\Controllers\UserController@pruebas');
    Route::get('testingCategory', 'App\Http\Controllers\CategoryController@pruebas');
    Route::get('testingPost', 'App\Http\Controllers\PostController@pruebas');
    
    // ROUTE USER CONTROLLER
    Route::post('api/register', 'App\Http\Controllers\UserController@register');
    Route::post('api/login', 'App\Http\Controllers\UserController@login');
    Route::put('api/user/update', 'App\Http\Controllers\UserController@update');
    Route::post('api/user/upload', 'App\Http\Controllers\UserController@upload')->middleware(ApiAuthMiddleware::class);
    Route::get('api/user/avatar/{filename}', 'App\Http\Controllers\UserController@getImage');
    Route::get('api/user/detail/{id}', 'App\Http\Controllers\UserController@detail');