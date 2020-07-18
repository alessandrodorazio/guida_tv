<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->name('auth.')->group(function(){
    Route::post('/', 'AuthController@login')->name('login');
    Route::post('/register', 'AuthController@register')->name('register');
    Route::delete('/{sid}', 'AuthController@logout')->name('logout');

    Route::middleware('isAdmin')->name('canale.')->group(function() {
        Route::post('/{sid}/canali', 'CanaleController@store')->name('store');
        Route::put('/{sid}/canali/{id}', 'CanaleController@update')->name('update');
    });
    

    Route::middleware('isAdmin')->name('programma.')->group(function() {
        Route::post('/{sid}/programmi', 'ProgrammaController@store')->name('store');
        Route::put('/{sid}/programmi/{id}', 'ProgrammaController@update')->name('update');
    });
    
});

Route::prefix('canali')->name('canale.')->group(function(){
    Route::get('/', 'CanaleController@index')->name('index');
    Route::get('/{id}/palinsesto', 'PalinsestoController@canaleOggi')->name('alias.palinsesto.dataOdierna');
    Route::get('/{id}/palinsesto/{data}', 'PalinsestoController@dataCanalePersonalizzati')->name('alias.palinsesto.dataPersonalizzata');
});

Route::prefix('palinsesto')->name('palinsesto.')->group(function(){
    Route::get('/', 'PalinsestoController@ricerca')->name('ricerca');
    Route::get('/{data}', 'PalinsestoController@dataPersonalizzata')->name('dataPersonalizzata');
    Route::get('/' . Carbon::now()->format('Y-m-d') . '/{canale}', 'PalinsestoController@canaleOggi')->name('alias.canale.palinsestoOdierno');
    Route::get('/{data}/{canale}', 'PalinsestoController@dataCanalePersonalizzati')->name('dataCanalePersonalizzati');
});

Route::prefix('programmi')->name('programma.')->group(function(){
    Route::get('/{id}', 'ProgrammaController@show')->name('show');
    Route::get('/{id}/episodi', 'ProgrammaController@episodi')->name('episodi');
});

//import dalla rai
Route::get('/import/rai', 'RaiController@import');