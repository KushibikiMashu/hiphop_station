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

Route::get('/', 'MaterialUiSampleController@react');
Route::get('/video', 'MaterialUiSampleController@react');
Route::get('/video/{hash?}', 'MaterialUiSampleController@react');

Route::get('/battle', 'MaterialUiSampleController@react');
Route::get('/battle/{hash?}', 'MaterialUiSampleController@react');
Route::get('/channel', 'MaterialUiSampleController@react');
Route::get('/channel/{hash?}', 'MaterialUiSampleController@react');

Route::get('/music_video', 'MaterialUiSampleController@react');
Route::get('/interview', 'MaterialUiSampleController@react');
Route::get('/others', 'MaterialUiSampleController@react');
