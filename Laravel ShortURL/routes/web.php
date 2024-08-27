<?php

use App\Http\Controllers\URLController;
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
    return Inertia::render('Home');
})->name("home");

Route::get("/{shortUrl}", [URLController::class, "redirect"]);
Route::post("/", [URLController::class, "store"])->name("store.shorturl");

require __DIR__ . '/auth.php';
