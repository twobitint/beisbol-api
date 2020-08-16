<?php

use Illuminate\Support\Facades\Artisan;
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

Route::get('login/yahoo', 'LoginController@redirectToProvider');
Route::get('login/yahoo/callback', 'LoginController@handleProviderCallback');

Route::get('/explore/{model}', 'ExploreController@index');

Route::get('sync', function () {
    Artisan::call('sync');
});
