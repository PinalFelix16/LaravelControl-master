<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// --- AUTENTICACIÓN ---
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [App\Http\Controllers\AuthController::class, 'logout']);

// --- CRUD PRINCIPALES ---
Route::apiResource('alumnos', App\Http\Controllers\AlumnoController::class);
Route::apiResource('maestros', App\Http\Controllers\MaestroController::class);
Route::apiResource('clases', App\Http\Controllers\ClaseController::class);
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
Route::apiResource('nominas', App\Http\Controllers\NominaController::class);
Route::get('nominas/{id}/informe/print', [App\Http\Controllers\NominaController::class, 'informeNominaPrint']);
Route::get('nominas/{id}/informe/pdf',   [App\Http\Controllers\NominaController::class, 'informeNominaPdf']);

// --- EXTRAS ---
Route::get('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'expediente']);
Route::put('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'actualizarExpediente']);
Route::post('alumnos/{id}/beca', [App\Http\Controllers\AlumnoController::class, 'asignarBeca']);
Route::post('alumnos/{id}/descuento', [App\Http\Controllers\AlumnoController::class, 'asignarDescuento']);
Route::get('maestros/{id}/informe', [App\Http\Controllers\MaestroController::class, 'informe']);
Route::get('pagos/{id}/imprimir', [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::get('pagos/{id}/recibo-pdf', [App\Http\Controllers\PagoController::class, 'generarReciboPDF']);
Route::get('adeudos/exportar-pdf', [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);

// --- CORTES (LAS PRINCIPALES ABIERTAS PARA QUE FUNCIONE EL FRONT) ---
Route::get('corte-caja', [App\Http\Controllers\CorteController::class, 'corteCaja']);
Route::post('realizar-corte', [App\Http\Controllers\CorteController::class, 'realizarCorte']);

// --- PROTEGIDAS (SOLO SI TIENES LOGIN EN EL FRONTEND, SINO QUÍTALO) ---
Route::middleware('auth:sanctum')->group(function () {

});

Route::get('corte-caja', [App\Http\Controllers\CorteController::class, 'corteCaja']);
// Consultar todos los cortes históricos
    Route::get('cortes', [App\Http\Controllers\CorteController::class, 'index']);
    // Consultar movimientos de un corte específico
    Route::get('cortes/{id_corte}/movimientos', [App\Http\Controllers\CorteController::class, 'getPagosPorCorte']);
    // Cortes históricos por año
    Route::get('cortes/historico/{anio}', [App\Http\Controllers\CorteController::class, 'getCortesPorAnio']);
    // Cortes históricos por año y mes
    Route::get('cortes/historico/{anio}/{mes}', [App\Http\Controllers\CorteController::class, 'getCortesPorMes']);

    Route::post('realizar-corte', [App\Http\Controllers\CorteController::class, 'realizarCorte']);

    Route::get('cortes/{anio}/{mes}/movimientos', [App\Http\Controllers\CorteController::class, 'getMovimientosPorAnioMes']);

    Route::get('cortes/{id_corte}/detalle', [App\Http\Controllers\CorteController::class, 'getDetalleCorte']);

// --- RUTA FAKE PARA EVITAR ERROR DE LOGIN EN SANCTUM ---
Route::get('/login', function() {
    return response()->json(['error' => 'No autenticado'], 401);
})->name('login');
