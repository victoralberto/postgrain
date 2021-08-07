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

Route::prefix("api")->group(function () {
    Route::get('/', 'APIController@index')->name('index');
    Route::post('/store', 'APIController@store')->name('index');
    Route::get('/notificacao', 'APIController@notificacao')->name('notificacao');
    Route::get('/{id}/concluido', 'APIController@concluir_lembrete')->name('conclui');
    Route::get('/show/{id}', 'APIController@show')->name('show');
    Route::get('/update/{id}', 'APIController@update')->name('update');
    Route::get('/{id}/delete', 'APIController@destroy')->name('destroy');
});
