<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\StokBarangController;


Route::resource('barangs', BarangController::class);
Route::resource('stok-barangs', StokBarangController::class);
Route::resource('transaksis', TransaksiController::class);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
