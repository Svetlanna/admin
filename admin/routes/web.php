<?php

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
http://127.0.0.1:8000/home

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/newpeople', 'HomeController@newPeople');
Route::get('/getUpdate', 'HomeController@getUpdate');
Route::put('/newtUpdate', 'HomeController@newtUpdate');
Route::post('/deletePeople', 'HomeController@deletePeople');
