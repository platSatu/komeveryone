<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KomDigital\DashboardKomController;
use App\Http\Controllers\Kmd\Superadmin\BookController;
use App\Http\Controllers\Kmd\Dashboard\PersembahanController;
use App\Http\Controllers\KomDigital\KomModulController;
use App\Http\Controllers\KomDigital\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/books/no-books', [BookController::class, 'apiNoBooks']);
Route::get('/kom/pertanyaan/{videoId}', [DashboardKomController::class, 'getPertanyaanByVideo']);
Route::post('/notification', [PersembahanController::class, 'callbackMidtrans']);

Route::get('/kom/pertanyaan/{videoId}', [DashboardKomController::class, 'getPertanyaanByVideo']);
Route::get('/api/kom-modul/{modulId}/ordered-videos', [KomModulController::class, 'getOrderedVideos']);
Route::get('/kom-digital/api/rayon-search', [AuthController::class, 'rayonSearch']);
Route::get('/kom-digital/api/asal-rayon', [AuthController::class, 'asalRayon']);
