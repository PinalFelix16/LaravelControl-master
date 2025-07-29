<?php

use Illuminate\Support\Facades\Route;
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
Route::get('miscelanea', [MiscelaneaController::class, 'index']);


// Ejemplo: GET /api/users/lista-usuarios
Route::get('users/lista-usuarios', [UserController::class, 'listaUsuarios']);

Route::middleware('api')->group(function () {
    Route::get('alumnos', [AlumnoController::class, 'index']);
    Route::get('alumnos/datos-combinados', [AlumnoController::class, 'mostrarDatosCombinados']);
    Route::get('alumnos/{id}', [AlumnoController::class, 'show']);
    Route::post('alumnos', [AlumnoController::class, 'store']);
    Route::put('alumnos/{id}', [AlumnoController::class, 'update']);
    Route::delete('alumnos/{id}', [AlumnoController::class, 'destroy']);
    Route::get('/alumnos-detalles/{id_alumno}', [AlumnoController::class, 'getAlumnosConDetalles']);
    Route::put('/alumnos-baja/{id}', [AlumnoController::class, 'bajaAlumno']);
    Route::put('/alumnos/{id}/alta', [AlumnoController::class, 'altaAlumno']);
    Route::get('/alumnos-combinados/pdf', [AlumnoController::class, 'PDFmostrarDatosCombinados']);

});

Route::middleware('api')->group(function () {
    /*Route::get('clases', [ClaseController::class, 'index']);
    Route::get('clases/{id}', [ClaseController::class, 'show']);
    Route::put('clases/{id}', [ClaseController::class, 'update']);
    Route::delete('clases/{id}', [ClaseController::class, 'destroy']);*/
    Route::post('/clases', [ClaseController::class, 'store']);
    Route::get('/alumnos-clases/{id_programa}/{id_clase?}', [ClaseController::class, 'obtenerDatos']);
    Route::post('/mostrar-informacion-alumno', [ClaseController::class, 'mostrarInformacionAlumno']);
    Route::post('/actualizar-beca', [ClaseController::class, 'actualizarBeca']);
});
Route::middleware('api')->group(function () {
    Route::get('maestros', [MaestroController::class, 'index']);
    Route::get('maestros/{id}', [MaestroController::class, 'show']);
    Route::post('maestros', [MaestroController::class, 'store']);
    Route::put('maestros/{id}', [MaestroController::class, 'update']);
    Route::delete('maestros/{id}', [MaestroController::class, 'destroy']);
    Route::get('/lista-maestros', [MaestroController::class, 'obtenerMaestros']);
    Route::put('/maestros-status/{id}', [MaestroController::class, 'actualizarStatus']);
});

Route::middleware('api')->group(function () {
    Route::get('nominas', [NominaController::class, 'index']);
    Route::get('nominas/{id}', [NominaController::class, 'show']);
    Route::post('nominas', [NominaController::class, 'store']);
    Route::put('nominas/{id}', [NominaController::class, 'update']);
    Route::delete('nominas/{id}', [NominaController::class, 'destroy']);
    Route::get('nominas/anios/{anio}', [NominaController::class, 'mostrarNominas']);
    Route::get('anios', [NominaController::class, 'mostrarAnios']);
    Route::post('generar-nomina', [NominaController::class, 'generarNomina']);
    Route::get('informe-nomina/{id_nomina}', [NominaController::class, 'informeNomina']);
});

Route::middleware('api')->group(function () {
    Route::get('adeudos-programas', [AdeudoProgramaController::class, 'index']);
    Route::post('adeudos-programas', [AdeudoProgramaController::class, 'store']);
    Route::get('adeudos-programas/{id}', [AdeudoProgramaController::class, 'show']);
    Route::put('adeudos-programas/{id}', [AdeudoProgramaController::class, 'update']);
    Route::delete('adeudos-programas/{id}', [AdeudoProgramaController::class, 'destroy']);
    Route::get('/adeudos-programas/alumno/{id_alumno}', [AdeudoProgramaController::class, 'filterByAlumno']);

});
Route::middleware('api')->group(function () {
    Route::get('/cortes', [CorteController::class, 'index']);
    Route::get('/corte-caja', [CorteController::class, 'corteCaja']);
    Route::get('/cortes/{anio}', [CorteController::class, 'getCortesPorAnio']);
    Route::get('/cortes/{anio}/{mes}', [CorteController::class, 'getCortesPorMes']);
    Route::get('/info-cortes/{id_corte}', [CorteController::class, 'getPagosPorCorte']);
    Route::post('/realizar-corte', [CorteController::class, 'realizarCorte']);
    Route::post('/miscelanea', [CorteController::class, 'miscelanea']);
});

Route::middleware('api')->group(function () {
Route::get('/adeudos/{id}', [ExpedienteAlumnoController::class, 'getAdeudosPorAlumno']);
Route::get('/pagos/{id}', [ExpedienteAlumnoController::class, 'getPagosPorAlumno']);
Route::get('/alumno-programas/{id_alumno}', [ExpedienteAlumnoController::class, 'getProgramasPorAlumno']);
Route::get('/informacion', [ExpedienteAlumnoController::class, 'obtenerInformacionVisitas']);
Route::get('/programas/{id}', [ExpedienteAlumnoController::class, 'obtenerProgramas']);
Route::post('/inscripcion/{id}', [ExpedienteAlumnoController::class, 'registrarInscripcion']);
Route::post('/registrar-recargo/{id}', [ExpedienteAlumnoController::class, 'registrarRecargo']);
Route::post('/registrar-visita/{id_alumno}/{id_clase}', [ExpedienteAlumnoController::class, 'registrarVisita']);
Route::post('/registrar-programa', [ExpedienteAlumnoController::class, 'agregarPrograma']);
Route::post('/procesar-pagos', [ExpedienteAlumnoController::class, 'accionPago']);
});

Route::middleware('api')->group(function () {
    /*Route::get('/pagos', [PagoController::class, 'index']);
    Route::post('/pagos', [PagoController::class, 'store']);
    Route::get('/pagos/{id}', [PagoController::class, 'show']);
    Route::delete('/pagos/{id}', [PagoController::class, 'destroy']);*/
    Route::apiResource('pagos', PagoController::class);
    Route::get('/pagos/{id}/recibo', [PagoController::class, 'generarReciboPDF']);
});

Route::middleware('api')->group(function () {
    Route::get('/alumno/descuento', [DescuentosController::class, 'getconfigurarDescuento']);
    Route::post('/configurar-descuento',[DescuentosController::class, 'updateDescuento'] );
});

Route::middleware('api')->group(function () {
    Route::delete('/eliminar-adeudo', [CargosController::class, 'eliminarCargoPrimario']);
    Route::delete('/eliminar-adeudo-secundario', [CargosController::class, 'eliminarCargoSecundario']);
    Route::delete('/eliminar-programa', [CargosController::class, 'removerPrograma']);
    //Route::get('/eliminar-pago-clase', [CargosController::class, 'eliminarPagoClase']);

});

Route::middleware('api')->group(function () {
Route::post('/buscar-alumnos', [ImprimirController::class, 'buscarAlumnos']);
Route::post('/deudores', [ImprimirController::class, 'consultarDeudores']);
});

Route::middleware('api')->group(function () {
Route::post('/login', [AuthController::class, 'login']);
});

Route::apiResource('clases', ClaseController::class);
Route::apiResource('maestros', MaestroController::class);
Route::apiResource('programas-predefinidos', ProgramaPredefinidoController::class);
Route::post('/recargos', [RecargoController::class, 'agregarRecargos']);
Route::apiResource('users', UserController::class);
Route::apiResource('miscelanea', MiscelaneaController::class);

