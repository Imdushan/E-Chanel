<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Events\Hello;

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
Auth::routes();

//Route::get('/login', 'HomeController@index');
Route::get('/', 'HomeController@landinngPage');
// Route::get('/loging', 'HomeController@login');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'HomeController@dashboard');





// --------DPD -> AS route-----------
Route::group(['middleware' => ['AS']], function () {

    Route::get('/as/dashboard', function () { return view('Dashboard'); });
    Route::get('/as/farmer-list', function () { return view('Dashboard'); });
    Route::get('/as/producer-society-list', function () { return view('Dashboard'); });
});

