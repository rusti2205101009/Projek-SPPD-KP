<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/spts/{id}/cetak', [WordController::class, 'cetakSpt'])->name('spts.cetak');

Route::get('/export/full-rekap', [ExportController::class, 'fullRekap']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
