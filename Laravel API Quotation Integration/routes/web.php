<?php

use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Route;

Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation');

require __DIR__.'/auth.php';
