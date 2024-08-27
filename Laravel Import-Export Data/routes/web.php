<?php


use App\Http\Controllers\Companies;
use App\Models\Company;
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

Route::get('/import', function () {
    return Inertia::render('Import');
})->name("import");

Route::get('/export', function () {
    return Inertia::render('Export', [
        'companies' => Company::all()
    ]);
})->name("export");

Route::get('/export/data', [Companies::class, 'export'])->name('export.company');
Route::post('/import', [Companies::class, 'import'])->name('import.company');

require __DIR__.'/auth.php';