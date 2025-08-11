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
Route::get('alumnos',                  [AlumnoController::class, 'index']);
Route::get('alumnos/datos-combinados',[AlumnoController::class, 'datosCombinados']);
Route::get('alumnos/{id}/expediente', [AlumnoController::class, 'expediente'])->whereNumber('id');
Route::put('alumnos/{id}/expediente', [AlumnoController::class, 'actualizarExpediente'])->whereNumber('id');

// ...
Route::get('pagos/{id_alumno}', [PagoController::class, 'byAlumno'])
    ->whereNumber('id_alumno');

// ---------- CLASES ----------
Route::get('clases',        [ClaseController::class, 'index']);
Route::get('clases/{id}',   [ClaseController::class, 'show'])->whereNumber('id');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('clases',      [ClaseController::class, 'store']);
    Route::put('clases/{id}',  [ClaseController::class, 'update'])->whereNumber('id');
    Route::delete('clases/{id}', [ClaseController::class, 'destroy'])->whereNumber('id');
});

// (Opcional) Fallback JSON para 404 en API
Route::fallback(function () {
    return response()->json(['message' => 'Ruta no encontrada.'], 404);
});


//APIs adicionales
Route::apiResource('programas', App\Http\Controllers\ProgramaController::class);
Route::apiResource('pagos', App\Http\Controllers\PagoController::class);
Route::apiResource('usuarios', App\Http\Controllers\UsuarioController::class);
Route::apiResource('becas', App\Http\Controllers\BecaController::class);
Route::apiResource('descuentos', App\Http\Controllers\DescuentoController::class);
Route::apiResource('roles', App\Http\Controllers\RolController::class);
Route::apiResource('pagos-programas', App\Http\Controllers\PagoProgramaController::class);
Route::apiResource('pagos-fragmentados', App\Http\Controllers\PagoFragmentadoController::class);
Route::apiResource('pagos-secundarios', App\Http\Controllers\PagoSecundarioController::class);
Route::apiResource('adeudos-programas', App\Http\Controllers\AdeudoProgramaController::class);
Route::apiResource('adeudos-fragmentados', App\Http\Controllers\AdeudoFragmentadoController::class);
Route::apiResource('adeudos-secundarios', App\Http\Controllers\AdeudoSecundarioController::class);
Route::apiResource('miscelanea', App\Http\Controllers\MiscelaneaController::class);
Route::get('pagos/{id}/imprimir', [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::get('pagos/{id}/recibo-pdf', [App\Http\Controllers\PagoController::class, 'generarReciboPDF']);
Route::get('adeudos/exportar-pdf', [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);

//CORTES(CONSULTAS)
// Caja / pendientes
Route::get('corte-caja', [App\Http\Controllers\CorteController::class, 'corteCaja']);
Route::post('realizar-corte', [App\Http\Controllers\CorteController::class, 'realizarCorte']);

// Listados JSON
Route::get('cortes', [App\Http\Controllers\CorteController::class, 'mensual']); // ?year&month
Route::get('cortes/historico/{anio}', [App\Http\Controllers\CorteController::class, 'getCortesPorAnio']);
Route::get('cortes/historico/{anio}/{mes}', [App\Http\Controllers\CorteController::class, 'getCortesPorMes']);

// ✔ JSON semanal (NO bajo /reportes para que no choque con el PDF)
Route::get('cortes/por-semana', [App\Http\Controllers\CorteController::class, 'porSemana']);

// Detalle JSON
Route::get('cortes/{id_corte}', [App\Http\Controllers\CorteController::class, 'show']);
Route::get('cortes/{id_corte}/movimientos', [App\Http\Controllers\CorteController::class, 'getPagosPorCorte']);

// Reportes PDF
Route::prefix('reportes/cortes')->group(function () {
    // ✔ PDF semanal (nombre correcto del método)
    Route::get('por-semana', [App\Http\Controllers\CorteController::class, 'reporteSemana']);

    // ✔ Nombres que EXISTEN en el controller
    Route::get('historico/{anio}/{mes}', [App\Http\Controllers\CorteController::class, 'reporteMes'])
        ->whereNumber('anio')->whereNumber('mes');

    Route::get('historico/{anio}', [App\Http\Controllers\CorteController::class, 'reporteAnio'])
        ->whereNumber('anio');

    // Detalle de un corte
    Route::get('{id}', [App\Http\Controllers\CorteController::class, 'reporteDetalle'])->whereNumber('id');
});


//NOMINA
Route::get('nominas/anios',            [App\Http\Controllers\NominaController::class, 'mostrarAnios']);
Route::get('nominas/mostrar/{anio}',   [App\Http\Controllers\NominaController::class, 'mostrarNominas'])->whereNumber('anio');
Route::post('nominas/generar',         [App\Http\Controllers\NominaController::class, 'generarNomina']);

Route::get('nominas/{id}/informe',       [App\Http\Controllers\NominaController::class, 'informeNomina']);
Route::get('nominas/{id}/informe/print', [App\Http\Controllers\NominaController::class, 'informeNominaPrint']);
Route::get('nominas/{id}/informe/pdf',   [App\Http\Controllers\NominaController::class, 'informeNominaPdf']);

Route::apiResource('nominas', App\Http\Controllers\NominaController::class);


// --- RUTA FAKE PARA EVITAR ERROR DE LOGIN EN SANCTUM ---
Route::get('/login', function() {
    return response()->json(['error' => 'No autenticado'], 401);
})->name('login');
