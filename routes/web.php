<?php

use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ArbitroController;
use App\Http\Controllers\CompetidorController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\KataController;
use App\Http\Controllers\LicenciaTipoController;
use App\Http\Controllers\SistemaCompetenciaController;
use App\Http\Controllers\EstilosKarateController;
use App\Http\Controllers\ModalidadController;
use App\Http\Controllers\OrganizacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TableroController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;
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

    Route::get('/organizaciones', [OrganizacionController::class, 'index'])->name('organizaciones.index');
    Route::get('/organizaciones/ajax/list', [OrganizacionController::class, 'ajaxList'])->name('organizaciones.ajax.list');
    Route::post('/organizaciones', [OrganizacionController::class, 'store'])->name('organizaciones.store');
    Route::patch('/organizaciones/{organizacion}', [OrganizacionController::class, 'update'])->name('organizaciones.update');
    Route::patch('/organizaciones/{organizacion}/estado', [OrganizacionController::class, 'toggleStatus'])->name('organizaciones.toggle-status');
    Route::delete('/organizaciones/{organizacion}', [OrganizacionController::class, 'destroy'])->name('organizaciones.destroy');
    Route::get('/organizaciones/{organizacion}/competidores', [CompetidorController::class, 'index'])->name('organizaciones.competidores.index');
    Route::get('/organizaciones/{organizacion}/competidores/ajax/list', [CompetidorController::class, 'ajaxList'])->name('organizaciones.competidores.ajax.list');
    Route::post('/organizaciones/{organizacion}/competidores', [CompetidorController::class, 'store'])->name('organizaciones.competidores.store');
    Route::patch('/organizaciones/{organizacion}/competidores/{competidor}/estado', [CompetidorController::class, 'toggleStatus'])->name('organizaciones.competidores.toggle-status');
    Route::delete('/organizaciones/{organizacion}/competidores/{competidor}', [CompetidorController::class, 'destroy'])->name('organizaciones.competidores.destroy');

    Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos.index');
    Route::get('/torneos/ajax/list', [TorneoController::class, 'ajaxList'])->name('torneos.ajax.list');
    Route::post('/torneos', [TorneoController::class, 'store'])->name('torneos.store');
    Route::patch('/torneos/{torneo}', [TorneoController::class, 'update'])->name('torneos.update');
    Route::patch('/torneos/{torneo}/costos-inscripcion', [TorneoController::class, 'updateCostos'])->name('torneos.costos.update');
    Route::patch('/torneos/{torneo}/estado', [TorneoController::class, 'toggleStatus'])->name('torneos.toggle-status');
    Route::delete('/torneos/{torneo}', [TorneoController::class, 'destroy'])->name('torneos.destroy');
    Route::get('/torneos/{torneo}/arbitros', [ArbitroController::class, 'index'])->name('arbitros.index');
    Route::post('/torneos/{torneo}/arbitros', [ArbitroController::class, 'store'])->name('arbitros.store');
    Route::patch('/torneos/{torneo}/arbitros/{arbitro}', [ArbitroController::class, 'update'])->name('arbitros.update');
    Route::delete('/torneos/{torneo}/arbitros/{arbitro}', [ArbitroController::class, 'destroy'])->name('arbitros.destroy');
    Route::get('/torneos/{torneo}/inscripciones', [InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::get('/torneos/{torneo}/inscripciones/ajax/list', [InscripcionController::class, 'ajaxList'])->name('inscripciones.ajax.list');
    Route::get('/torneos/{torneo}/inscripciones/imprimir', [InscripcionController::class, 'print'])->name('inscripciones.print');
    Route::post('/torneos/{torneo}/inscripciones/organizaciones', [InscripcionController::class, 'storeOrganizacion'])->name('inscripciones.organizaciones.store');
    Route::delete('/torneos/{torneo}/inscripciones/organizaciones/{inscripcion}', [InscripcionController::class, 'destroyOrganizacion'])->name('inscripciones.organizaciones.destroy');
    Route::post('/torneos/{torneo}/inscripciones/competidores', [InscripcionController::class, 'storeCompetidor'])->name('inscripciones.competidores.store');
    Route::get('/torneos/{torneo}/inscripciones/{inscripcion}/participantes', [InscripcionController::class, 'participantes'])->name('inscripciones.participantes');
    Route::post('/torneos/{torneo}/inscripciones/{inscripcion}/participantes', [InscripcionController::class, 'storeParticipante'])->name('inscripciones.participantes.store');
    Route::get('/torneos/{torneo}/modalidades', [ModalidadController::class, 'index'])->name('modalidades.index');
    Route::get('/torneos/{torneo}/modalidades/ajax/list', [ModalidadController::class, 'ajaxList'])->name('modalidades.ajax.list');
    Route::get('/torneos/{torneo}/modalidades/imprimir', [ModalidadController::class, 'print'])->name('modalidades.print');
    Route::get('/torneos/{torneo}/modalidades/{modalidad}', [ModalidadController::class, 'show'])->name('modalidades.show');
    Route::post('/torneos/{torneo}/categorias', [ModalidadController::class, 'storeCategoria'])->name('categorias.store');
    Route::patch('/torneos/{torneo}/modalidades/{modalidad}/categorias/{categoria}', [ModalidadController::class, 'updateCategoria'])->name('categorias.update');
    Route::delete('/torneos/{torneo}/modalidades/{modalidad}/categorias/{categoria}', [ModalidadController::class, 'destroyCategoria'])->name('categorias.destroy');
    Route::post('/torneos/{torneo}/modalidades', [ModalidadController::class, 'store'])->name('modalidades.store');
    Route::patch('/torneos/{torneo}/modalidades/{modalidad}', [ModalidadController::class, 'update'])->name('modalidades.update');
    Route::delete('/torneos/{torneo}/modalidades/{modalidad}', [ModalidadController::class, 'destroy'])->name('modalidades.destroy');

    Route::get('/estiloskarate', [EstilosKarateController::class, 'index'])->name('estiloskarate.index');
    Route::get('/estiloskarate/ajax/list', [EstilosKarateController::class, 'ajaxList'])->name('estiloskarate.ajax.list');
    Route::post('/estiloskarate', [EstilosKarateController::class, 'store'])->name('estiloskarate.store');
    Route::patch('/estiloskarate/{estiloskarate}', [EstilosKarateController::class, 'update'])->name('estiloskarate.update');
    Route::patch('/estiloskarate/{estiloskarate}/estado', [EstilosKarateController::class, 'toggleStatus'])->name('estiloskarate.toggle-status');
    Route::delete('/estiloskarate/{estiloskarate}', [EstilosKarateController::class, 'destroy'])->name('estiloskarate.destroy');

    Route::get('/licencias', [LicenciaTipoController::class, 'index'])->name('licencias.index');
    Route::post('/licencias', [LicenciaTipoController::class, 'store'])->name('licencias.store');
    Route::patch('/licencias/{licencia}', [LicenciaTipoController::class, 'update'])->name('licencias.update');
    Route::delete('/licencias/{licencia}', [LicenciaTipoController::class, 'destroy'])->name('licencias.destroy');

    Route::get('/sistema-competencia', [SistemaCompetenciaController::class, 'index'])->name('sistema-competencia.index');
    Route::get('/sistema-competencia/ajax/list', [SistemaCompetenciaController::class, 'ajaxList'])->name('sistema-competencia.ajax.list');
    Route::post('/sistema-competencia', [SistemaCompetenciaController::class, 'store'])->name('sistema-competencia.store');
    Route::patch('/sistema-competencia/{sistemaCompetencia}', [SistemaCompetenciaController::class, 'update'])->name('sistema-competencia.update');
    Route::patch('/sistema-competencia/{sistemaCompetencia}/estado', [SistemaCompetenciaController::class, 'toggleStatus'])->name('sistema-competencia.toggle-status');
    Route::delete('/sistema-competencia/{sistemaCompetencia}', [SistemaCompetenciaController::class, 'destroy'])->name('sistema-competencia.destroy');

    Route::get('/katas', [KataController::class, 'index'])->name('katas.index');
    Route::get('/katas/ajax/list', [KataController::class, 'ajaxList'])->name('katas.ajax.list');
    Route::post('/katas', [KataController::class, 'store'])->name('katas.store');
    Route::patch('/katas/{kata}', [KataController::class, 'update'])->name('katas.update');
    Route::patch('/katas/{kata}/estado', [KataController::class, 'toggleStatus'])->name('katas.toggle-status');
    Route::delete('/katas/{kata}', [KataController::class, 'destroy'])->name('katas.destroy');

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/ajax/list', [RoleController::class, 'ajaxList'])->name('roles.ajax.list');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::patch('/roles/{role}/estado', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/ajax/list', [UsuarioController::class, 'ajaxList'])->name('usuarios.ajax.list');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::patch('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/estado', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle-status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
