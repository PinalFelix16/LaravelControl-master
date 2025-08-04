<?php

use Illuminate\Support\Facades\Route;

// Controladores
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\AdeudoProgramaController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\ExpedienteAlumnoController;
use App\Http\Controllers\DescuentosController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\ImprimirController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProgramaPredefinidoController;
use App\Http\Controllers\RecargoController;
use App\Http\Controllers\MiscelaneaController;

// ===============================
//  RUTAS BÁSICAS
// ===============================
Route::get('miscelanea', [MiscelaneaController::class, 'index']);
Route::get('users/lista-usuarios', [UserController::class, 'listaUsuarios']);

// ===============================
//  GRUPO API
// ===============================
Route::middleware('api')->group(function () {

    /**
     * =========================
     *  RUTAS ESPECÍFICAS
     * =========================
     * Estas siempre deben ir antes de las rutas dinámicas con {id}
     */
    Route::get('alumnos/datos-combinados', [AlumnoController::class, 'datosCombinados']);
    Route::get('alumnos-detalles/{id_alumno}', [AlumnoController::class, 'getAlumnosConDetalles']);
    Route::get('alumnos-combinados/pdf', [AlumnoController::class, 'PDFmostrarDatosCombinados']);

    /**
     * =========================
     *  RUTAS REST DE ALUMNOS
     * =========================
     */
    Route::get('alumnos', [AlumnoController::class, 'index']);
    Route::get('alumnos/{id}', [AlumnoController::class, 'show']);
    Route::post('alumnos', [AlumnoController::class, 'store']);
    Route::put('alumnos/{id}', [AlumnoController::class, 'update']);
    Route::delete('alumnos/{id}', [AlumnoController::class, 'destroy']);

    // Extras para bajas/altas de alumnos
    Route::put('/alumnos/{id}/baja', [AlumnoController::class, 'bajaAlumno']);
    Route::put('/alumnos/{id}/alta', [AlumnoController::class, 'altaAlumno']);


    /**
     * =========================
     *  OTRAS RUTAS DE TUS MÓDULOS
     * =========================
     * Pagos, adeudos, maestros, nómina, etc.
     */

    // Rutas de Pagos
    Route::apiResource('pagos', PagoController::class);

    // Rutas de Clases
    Route::apiResource('clases', ClaseController::class);

    // Rutas de Maestros
    Route::apiResource('maestros', MaestroController::class);

    // Rutas de Nómina
    Route::apiResource('nomina', NominaController::class);

    // Rutas de Adeudos
    Route::apiResource('adeudos', AdeudoProgramaController::class);

    // Rutas de Cortes
    Route::apiResource('cortes', CorteController::class);

    // Rutas de Expediente de Alumnos
    Route::apiResource('expediente-alumnos', ExpedienteAlumnoController::class);

    // Rutas de Descuentos
    Route::apiResource('descuentos', DescuentosController::class);

    // Rutas de Cargos
    Route::apiResource('cargos', CargosController::class);

    // Rutas de Imprimir (si son endpoints específicos puedes agregarlos aquí)
    // Route::get('imprimir/...', [ImprimirController::class, '...']);

    // Rutas de Programas Predefinidos
    Route::apiResource('programas-predefinidos', ProgramaPredefinidoController::class);

    // Rutas de Recargos
    Route::apiResource('recargos', RecargoController::class);

});
