<?php

use App\Http\Controllers\Concursuri\ConcursuriController;
use App\Http\Controllers\GestioneazaController;
use App\Http\Controllers\IstoricConcursuriController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Acasa');
})->name('acasa');

Route::group(['prefix' => 'istoric-concursuri'], function () {
    Route::get('/asociaza', [IstoricConcursuriController::class, 'index'])->name('asociaza');
    Route::post('/asociaza', [IstoricConcursuriController::class, 'search'])->name('store.asociaza');

    Route::get('/palmares', [IstoricConcursuriController::class, 'view'])->name('vizualizeaza');
});

Route::group(['prefix' => 'concursuri'], function () {
    Route::get('/', [ConcursuriController::class, 'index'])->name('concursuri');
    Route::get('/concursuri-2024', [ConcursuriController::class, 'concursuri2024'])->name('concursuri.2024');
    Route::post('/get/concurs', [ConcursuriController::class, 'getConcurs'])->name('get.concurs');
    Route::post('/get/mansa/details', [ConcursuriController::class, 'getMansaDetails'])->name('get.mansa.details');
    Route::post('/inscriere', [ConcursuriController::class, 'inscriere'])->name('inscriere');
});

Route::group(['prefix' => 'gestioneaza'], function () {
    Route::get('/concurs/{id}', [GestioneazaController::class, 'index'])->name('gestioneaza.concurs');

    Route::post('/get/participant', [GestioneazaController::class, 'getParticipant'])->name('get.participant');
    Route::post('/get/standuri', [GestioneazaController::class, 'getStanduri'])->name('get.standuri');
    Route::post('/update/participant/inscrieri', [GestioneazaController::class, 'updateParticipant'])->name('update.user.inscriere');
});

require __DIR__ . '/auth.php';
