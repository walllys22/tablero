<?php

use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ModalidadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TableroController;
use App\Http\Controllers\TorneoController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/kumite/tablero', [TableroController::class, 'kumite'])->name('tablero.kumite');

Route::get('/kata/tablero', [TableroController::class, 'kata'])->name('tablero.kata');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/limpiar-cache', function () {
        Artisan::call('optimize:clear');

        return back()->with('status', 'Caches limpiadas correctamente.');
    })->name('cache.clear');

    Route::view('/eventos/registro', 'eventos.registro')->name('eventos.registro');

    Route::get('/people', [PersonaController::class, 'index'])->name('people.browse');
    Route::get('/people/ajax/list', [PersonaController::class, 'ajaxList'])->name('people.ajax.list');
    Route::post('/people', [PersonaController::class, 'store'])->name('people.store');
    Route::patch('/people/{persona}', [PersonaController::class, 'update'])->name('people.update');
    Route::patch('/people/{persona}/estado', [PersonaController::class, 'toggleStatus'])->name('people.toggle-status');
    Route::delete('/people/{persona}', [PersonaController::class, 'destroy'])->name('people.destroy');

    Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos.index');
    Route::get('/torneos/ajax/list', [TorneoController::class, 'ajaxList'])->name('torneos.ajax.list');
    Route::post('/torneos', [TorneoController::class, 'store'])->name('torneos.store');
    Route::patch('/torneos/{torneo}', [TorneoController::class, 'update'])->name('torneos.update');
    Route::patch('/torneos/{torneo}/estado', [TorneoController::class, 'toggleStatus'])->name('torneos.toggle-status');
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy'])->name('torneos.destroy');
    Route::get('/torneos/{torneo}/modalidades', [ModalidadController::class, 'index'])->name('modalidades.index');
    Route::get('/torneos/{torneo}/modalidades/ajax/list', [ModalidadController::class, 'ajaxList'])->name('modalidades.ajax.list');
    Route::post('/torneos/{torneo}/modalidades', [ModalidadController::class, 'store'])->name('modalidades.store');
    Route::patch('/torneos/{torneo}/modalidades/{modalidad}', [ModalidadController::class, 'update'])->name('modalidades.update');
    Route::delete('/torneos/{torneo}/modalidades/{modalidad}', [ModalidadController::class, 'destroy'])->name('modalidades.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
