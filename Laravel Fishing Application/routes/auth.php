<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'autentificare'], function () {
    Route::get('/', [\App\Http\Controllers\Autentificare\AutentificareController::class, 'index'])->name('autentificare');
    Route::post('/', [\App\Http\Controllers\Autentificare\AutentificareController::class, 'autentificare'])->name('autentificare.request');
});

Route::group(['prefix' => 'inregistrare'], function () {
    Route::get('/', [\App\Http\Controllers\Autentificare\RegistrationController::class, 'redirect'])->name('inregistrare');
    Route::post('/', [\App\Http\Controllers\Autentificare\RegistrationController::class, 'store'])->name('inregistrare.store');
    Route::get('/login', [\App\Http\Controllers\Autentificare\RegistrationController::class, 'login'])->name('login');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
