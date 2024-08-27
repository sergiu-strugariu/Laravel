<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Autentificare\AuthentificationController;
use App\Http\Controllers\Autentificare\ProviderController;
use App\Http\Controllers\Autentificare\RegistrationController;

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\LacController;
use App\Http\Controllers\Dashboard\MansaController;
use App\Http\Controllers\Dashboard\StandController;
use App\Http\Controllers\Dashboard\ConcursController;


Route::group(['prefix' => 'autentificare'], function () {
    Route::get('/', [AuthentificationController::class, 'index'])->name('autentificare');
    Route::post('/', [AuthentificationController::class, 'autentificare'])->name('autentificare.request');
});

Route::group(['prefix' => '{provider}'], function () {
    Route::get('/redirect', [ProviderController::class, 'redirect'])->name('google.redirect');
    Route::get('/callback', [ProviderController::class, 'callback']);
});

Route::group(['prefix' => 'inregistrare'], function () {
    Route::get('/', [RegistrationController::class, 'index'])->name('inregistrare');
    Route::post('/', [RegistrationController::class, 'store'])->name('inregistrare.store');
    Route::get('/login', [RegistrationController::class, 'login'])->name('login');

    Route::get('/provider', [ProviderController::class, 'index'])->name('provider.inregistrare');
    Route::post('/provider/success', [ProviderController::class, 'update'])->name('provider.update');
});

Route::middleware('authentificare')->group(function () {
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/store/concurs', [ConcursController::class, 'store'])->name('store.concurs');
        Route::post('/store/lac', [LacController::class, 'store'])->name('store.lac');
        Route::post('/store/mansa', [MansaController::class, 'store'])->name('store.mansa');
        Route::post('/store/stand', [StandController::class, 'store'])->name('store.stand');
    });
});


Route::middleware('logout')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
