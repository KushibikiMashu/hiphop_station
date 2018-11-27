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

Route::get('/', 'SpaController@index');
Route::get('/video', 'SpaController@index');
Route::get('/video/{hash?}', 'SpaController@index');

Route::get('/battle', 'SpaController@index');
Route::get('/battle/{hash?}', 'SpaController@index');
Route::get('/channel', 'SpaController@index');
Route::get('/channel/{hash?}', 'SpaController@index');

Route::get('/music_video', 'SpaController@index');
Route::get('/interview', 'SpaController@index');
Route::get('/others', 'SpaController@index');
