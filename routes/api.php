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
Route::get('app/inventory','API\peminjamanInventoryController@allInventory');
Route::get('app/user','API\masalahLabController@alluser');
Route::get('app/ruangan','API\permintaanAplikasiController@ruangan');
Route::get('app/thnajaran','API\permintaanAplikasiController@thnAjaran');

//manage permintaan aplikasi
Route::get('permintaanAplikasi/finish/{id}','API\permintaanAplikasiController@finish');
Route::get('findpermintaanAplikasi','API\permintaanAplikasiController@search');
Route::apiResource('permintaanAplikasi','API\permintaanAplikasiController');

//manage masalah lab
Route::post('masalahLab/finish/{id}','API\masalahLabController@finish');
Route::get('masalahLab/start/{id}','API\masalahLabController@start');
Route::get('findMasalahLab','API\masalahLabController@search');
Route::apiResource('masalahLab','API\masalahLabController');

//manage uang kas
Route::apiResource('uangKas','API\uangKasController');
Route::get('findUangKas','API\uangKasController@search');
Route::get('lastSaldoKas','API\uangKasController@lastSaldo');

//manage catatan beli
Route::apiResource('catatanBeli','API\catatanBeliController');
Route::get('findCatatanBeli','API\catatanBeliController@search');
Route::get('catatanBeli/finish/{id}','API\catatanBeliController@lunasin');

//manage koperasi
Route::apiResource('koperasi','API\koperasiController');
Route::get('findKoperasi','API\koperasiController@search');
Route::get('koperasi/finish/{id}','API\koperasiController@bayar');

//manage koperasi
Route::apiResource('peminjamanInventory','API\peminjamanInventoryController');
Route::get('findPeminjamanInventory','API\peminjamanInventoryController@search');
Route::get('peminjamanInventory/finish/{id}','API\peminjamanInventoryController@dipulangkeun');

//manage koperasi
Route::apiResource('barangHilang','API\barangHilangController');
Route::get('barang-hilang/{id}','API\barangHilangController@show');
Route::get('findBarangHilang','API\barangHilangController@search');
Route::put('barangHilang/finish/{id}','API\barangHilangController@ditemukan');