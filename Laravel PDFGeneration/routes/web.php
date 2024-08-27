<?php

use App\Http\Controllers\PDFController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home', [
        'users' => User::all()
    ]);
})->name("home");

Route::get('report', [PDFController::class, 'report'])->name('report');

require __DIR__ . '/auth.php';
