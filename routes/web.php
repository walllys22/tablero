<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableroController;

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

Route::get('kumite/tablero', [TableroController::class, 'kumite'])->name('tablero.kumite');
Route::get('kata/tablero', [TableroController::class, 'kata'])->name('tablero.kata');

