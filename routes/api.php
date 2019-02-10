<?php

use Illuminate\Http\Request;

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

//manage user
Route::get('findUser','API\UserController@search');
Route::get('profile','API\UserController@profile');
Route::put('profile','API\UserController@updateProfile');
Route::apiResource('user','API\UserController');

//manage tahun ajaran
Route::apiResource('tahunAjaran','API\tahunAjaranController');
Route::get('findThnAjaran','API\tahunAjaranController@search');

//manage ruangan
Route::apiResource('ruangan','API\ruanganController');
Route::get('findRuangan','API\ruanganController@search');

//manage inventory
Route::apiResource('inventory','API\inventoryController');
Route::get('findInventory','API\inventoryController@search');

//manage inventory
Route::get('app/ruangan','API\permintaanAplikasiController@ruangan');
Route::get('app/thnajaran','API\permintaanAplikasiController@thnAjaran');
Route::get('findPermintaanAplikasi','API\permintaanAplikasiController@search');
Route::apiResource('permintaanAplikasi','API\permintaanAplikasiController');