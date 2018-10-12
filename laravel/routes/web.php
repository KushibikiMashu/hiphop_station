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

Route::get('/redis', 'CreepyNutsController@display');

Route::get('/react/sample', 'SampleController@react');

Route::get('/react/material/', 'MaterialUiSampleController@react');
Route::get('/react/material', 'MaterialUiSampleController@react');
Route::get('/react/material/video', 'MaterialUiSampleController@react');
Route::get('/react/material/video/{hash?}', 'MaterialUiSampleController@react');

Route::get('/react/material/battle', 'MaterialUiSampleController@react');
Route::get('/react/material/battle/{hash?}', 'MaterialUiSampleController@react');
Route::get('/react/material/channel', 'MaterialUiSampleController@react');
Route::get('/react/material/channel/{hash?}', 'MaterialUiSampleController@react');