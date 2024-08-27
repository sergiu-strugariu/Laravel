<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuotationController;

Route::post('/quotation', [QuotationController::class, 'getQuotation']);
