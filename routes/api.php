<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\PagoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Estas rutas se sirven bajo el prefijo /api automáticamente.
*/

// ---------- AUTH ----------
Route::post('login',  [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

// ---------- ALUMNOS ----------
Route::get('alumnos',                    [AlumnoController::class, 'index']);             // LISTAR
Route::get('alumnos/{id}',               [AlumnoController::class, 'show'])->whereNumber('id'); // VER UNO
Route::post('alumnos',                   [AlumnoController::class, 'store']);             // CREAR
Route::put('alumnos/{id}',               [AlumnoController::class, 'update'])->whereNumber('id'); // EDITAR
Route::patch('alumnos/{id}',             [AlumnoController::class, 'update'])->whereNumber('id'); // EDITAR parcial
// extras alumnos
Route::get('alumnos/datos-combinados',   [AlumnoController::class, 'datosCombinados']);
Route::get('alumnos/{id}/expediente',    [AlumnoController::class, 'expediente'])->whereNumber('id');
Route::put('alumnos/{id}/expediente',    [AlumnoController::class, 'actualizarExpediente'])->whereNumber('id');

// ALTA y BAJA (aceptar PUT y PATCH)
Route::match(['PUT','PATCH'], 'alumnos/{id}/baja', [AlumnoController::class, 'baja'])->whereNumber('id');
Route::match(['PUT','PATCH'], 'alumnos/{id}/alta', [AlumnoController::class, 'alta'])->whereNumber('id');

// ---------- PAGOS ----------
Route::get('pagos/{id_alumno}',          [PagoController::class, 'byAlumno'])->whereNumber('id_alumno');
Route::apiResource('pagos',              App\Http\Controllers\PagoController::class);
Route::get('pagos/{id}/imprimir',        [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::get('pagos/{id}/recibo-pdf',      [App\Http\Controllers\PagoController::class, 'generarReciboPDF']);

// ---------- USUARIOS / OTROS MÓDULOS ----------
Route::apiResource('maestros',           App\Http\Controllers\MaestroController::class);
Route::apiResource('programas',          App\Http\Controllers\ProgramaController::class);
Route::apiResource('usuarios',           App\Http\Controllers\UsuarioController::class);
Route::apiResource('becas',              App\Http\Controllers\BecaController::class);
Route::apiResource('descuentos',         App\Http\Controllers\DescuentoController::class);
Route::apiResource('roles',              App\Http\Controllers\RolController::class);
Route::apiResource('pagos-programas',    App\Http\Controllers\PagoProgramaController::class);
Route::apiResource('pagos-fragmentados', App\Http\Controllers\PagoFragmentadoController::class);
Route::apiResource('pagos-secundarios',  App\Http\Controllers\PagoSecundarioController::class);
Route::apiResource('adeudos-programas',  App\Http\Controllers\AdeudoProgramaController::class);
Route::apiResource('adeudos-fragmentados', App\Http\Controllers\AdeudoFragmentadoController::class);
Route::apiResource('adeudos-secundarios',  App\Http\Controllers\AdeudoSecundarioController::class);
Route::apiResource('miscelanea',         App\Http\Controllers\MiscelaneaController::class);
Route::get('adeudos/exportar-pdf',       [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);

// ---------- CORTES ----------
Route::get('corte-caja',                 [App\Http\Controllers\CorteController::class, 'corteCaja']);
Route::post('realizar-corte',            [App\Http\Controllers\CorteController::class, 'realizarCorte']);
Route::get('cortes',                     [App\Http\Controllers\CorteController::class, 'mensual']); // ?year&month
Route::get('cortes/historico/{anio}',    [App\Http\Controllers\CorteController::class, 'getCortesPorAnio']);
Route::get('cortes/historico/{anio}/{mes}', [App\Http\Controllers\CorteController::class, 'getCortesPorMes']);
Route::get('cortes/por-semana',          [App\Http\Controllers\CorteController::class, 'porSemana']);
Route::get('cortes/{id_corte}',          [App\Http\Controllers\CorteController::class, 'show']);
Route::get('cortes/{id_corte}/movimientos', [App\Http\Controllers\CorteController::class, 'getPagosPorCorte']);
Route::prefix('reportes/cortes')->group(function () {
    Route::get('por-semana',             [App\Http\Controllers\CorteController::class, 'reporteSemana']);
    Route::get('historico/{anio}/{mes}', [App\Http\Controllers\CorteController::class, 'reporteMes'])
        ->whereNumber('anio')->whereNumber('mes');
    Route::get('historico/{anio}',       [App\Http\Controllers\CorteController::class, 'reporteAnio'])
        ->whereNumber('anio');
    Route::get('{id}',                   [App\Http\Controllers\CorteController::class, 'reporteDetalle'])->whereNumber('id');
});

// ---------- CLASES ----------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('clases',                [ClaseController::class, 'store']);
    Route::put('clases/{id}',            [ClaseController::class, 'update'])->whereNumber('id');
    Route::delete('clases/{id}',         [ClaseController::class, 'destroy'])->whereNumber('id');
});
Route::get('clases', [ClaseController::class, 'index']);   // LISTAR
Route::get('clases/{id}', [ClaseController::class, 'show'])->whereNumber('id');
// --- RUTA FAKE PARA EVITAR ERROR DE LOGIN EN SANCTUM ---
Route::get('/login', function () {
    return response()->json(['error' => 'No autenticado'], 401);
})->name('login');

// (Dejar SIEMPRE al final)
Route::fallback(function () {
    return response()->json(['message' => 'Ruta no encontrada.'], 404);
});
