<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\Controller::class, 'index'])->name('home');
Route::get('/clasament-concursuri', [\App\Http\Controllers\ClasamentConcursuriController::class, 'view'])->name('clasament');
Route::post('/clasament-concursuri', [\App\Http\Controllers\ClasamentConcursuriController::class, 'get'])->name('get.concurs');

Route::get('/import', [\App\Http\Controllers\Controller::class, 'import'])->name('import');
Route::post('/import', [\App\Http\Controllers\Controller::class, 'storeCSV'])->name('importStore');

Route::group(['prefix' => 'istoric-concursuri'], function () {
    Route::get('/asociaza', [\App\Http\Controllers\IstoricConcursuriController::class, 'index'])->name('asociaza');
    Route::post('/asociaza', [\App\Http\Controllers\IstoricConcursuriController::class, 'search'])->name('store.asociaza');
    Route::get('/palmares', [\App\Http\Controllers\IstoricConcursuriController::class, 'view'])->name('vizualizeaza');
});

require __DIR__.'/auth.php';
