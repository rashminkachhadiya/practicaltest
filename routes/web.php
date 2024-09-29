<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::resource('price', 'App\Http\Controllers\PriceController');
Route::get('/allPrice', 'App\Http\Controllers\PriceController@getAll')->name('allPrice');
Route::post('simulate','App\Http\Controllers\PriceController@simulate')->name('price.simulate');
Route::get('reset','App\Http\Controllers\PriceController@reset')->name('price.reset');
