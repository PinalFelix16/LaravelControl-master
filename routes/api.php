<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\BecaController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PagoProgramaController;
use App\Http\Controllers\PagoFragmentadoController;
use App\Http\Controllers\PagoSecundarioController;
use App\Http\Controllers\AdeudoProgramaController;
use App\Http\Controllers\AdeudoFragmentadoController;
use App\Http\Controllers\AdeudoSecundarioController;
use App\Http\Controllers\MiscelaneaController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\CorteController;

// -----------------------------
// Autenticación
// -----------------------------
Route::post('login',  [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Ruta fake para Sanctum (evitar redirección web)
Route::get('/login', function () {
    return response()->json(['error' => 'No autenticado'], 401);
})->name('login');

// -----------------------------
// ALUMNOS – rutas personalizadas (ANTES del resource)
// -----------------------------
Route::get('alumnos/datos-combinados', [AlumnoController::class, 'datosCombinados']);
Route::get('alumnos/{id}/expediente',  [AlumnoController::class, 'expediente'])->whereNumber('id');
Route::put('alumnos/{id}/expediente',  [AlumnoController::class, 'actualizarExpediente'])->whereNumber('id');
Route::post('alumnos/{id}/beca',       [AlumnoController::class, 'asignarBeca'])->whereNumber('id');
Route::post('alumnos/{id}/descuento',  [AlumnoController::class, 'asignarDescuento'])->whereNumber('id');
Route::put('alumnos/{id}/alta',        [AlumnoController::class, 'altaAlumno'])->whereNumber('id');
Route::put('alumnos/{id}/baja',        [AlumnoController::class, 'bajaAlumno'])->whereNumber('id');

// -----------------------------
// CRUD RESTful principales
// -----------------------------
Route::apiResource('alumnos',            AlumnoController::class)->where(['alumno' => '[0-9]+']);
Route::apiResource('maestros',           MaestroController::class);
Route::apiResource('clases',             ClaseController::class);
Route::apiResource('programas',          ProgramaController::class);
Route::apiResource('pagos',              PagoController::class);
Route::apiResource('usuarios',           UsuarioController::class);
Route::apiResource('becas',              BecaController::class);
Route::apiResource('descuentos',         DescuentoController::class);
Route::apiResource('roles',              RolController::class);
Route::apiResource('pagos-programas',    PagoProgramaController::class);
Route::apiResource('pagos-fragmentados', PagoFragmentadoController::class);
Route::apiResource('pagos-secundarios',  PagoSecundarioController::class);
Route::apiResource('adeudos-programas',  AdeudoProgramaController::class);
Route::apiResource('adeudos-fragmentados', AdeudoFragmentadoController::class);
Route::apiResource('adeudos-secundarios',  AdeudoSecundarioController::class);
Route::apiResource('miscelanea',         MiscelaneaController::class);

// -----------------------------
// Nóminas
// -----------------------------
Route::get('nominas/anios',                [NominaController::class, 'mostrarAnios']);
Route::get('nominas/mostrar/{anio}',       [NominaController::class, 'mostrarNominas'])->whereNumber('anio');
Route::post('nominas/generar',             [NominaController::class, 'generarNomina']);
Route::get('nominas/{id}/informe',         [NominaController::class, 'informeNomina'])->whereNumber('id');
Route::get('nominas/{id}/informe/print',   [NominaController::class, 'informeNominaPrint'])->whereNumber('id');
Route::get('nominas/{id}/informe/pdf',     [NominaController::class, 'informeNominaPdf'])->whereNumber('id');
Route::apiResource('nominas',              NominaController::class);

// -----------------------------
// Cortes de caja (sin duplicados)
// -----------------------------
Route::get('corte-caja',                    [CorteController::class, 'corteCaja']);
Route::post('realizar-corte',               [CorteController::class, 'realizarCorte']);
Route::get('cortes',                        [CorteController::class, 'index']);
Route::get('cortes/{id_corte}/movimientos', [CorteController::class, 'getPagosPorCorte'])->whereNumber('id_corte');
Route::get('cortes/historico/{anio}',       [CorteController::class, 'getCortesPorAnio'])->whereNumber('anio');
Route::get('cortes/historico/{anio}/{mes}', [CorteController::class, 'getCortesPorMes'])->whereNumber('anio')->whereNumber('mes');
Route::get('cortes/{anio}/{mes}/movimientos', [CorteController::class, 'getMovimientosPorAnioMes'])->whereNumber('anio')->whereNumber('mes');
Route::get('cortes/{id_corte}/detalle',     [CorteController::class, 'getDetalleCorte'])->whereNumber('id_corte');

// -----------------------------
// Grupo protegido (si lo usas)
// -----------------------------
Route::middleware('auth:sanctum')->group(function () {
    // coloca aquí rutas que requieran login
});
