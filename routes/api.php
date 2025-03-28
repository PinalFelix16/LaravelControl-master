<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
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

// Grupo de rutas para Usuarios
Route::middleware('api')->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index']); // Obtener todos los usuarios
    Route::get('usuarios/{id}', [UsuarioController::class, 'show']); // Obtener un usuario específico por ID
    Route::post('usuarios', [UsuarioController::class, 'store']); // Crear un nuevo usuario
    Route::put('usuarios/{id}', [UsuarioController::class, 'update']); // Actualizar un usuario existente por ID
    Route::delete('usuarios/{id}', [UsuarioController::class, 'destroy']); // Eliminar un usuario por ID
    Route::post('/agregar-recargos', [UsuarioController::class, 'agregarRecargos']); // Agregar recargos a un usuario
});

// Grupo de rutas para Alumnos
Route::middleware('api')->group(function () {
    Route::get('alumnos', [AlumnoController::class, 'index']); // Obtener todos los alumnos
    Route::get('alumnos/datos-combinados', [AlumnoController::class, 'mostrarDatosCombinados']); // Obtener datos combinados de alumnos
    Route::get('alumnos/{id}', [AlumnoController::class, 'show']); // Obtener un alumno específico por ID
    Route::post('alumnos', [AlumnoController::class, 'store']); // Crear un nuevo alumno
    Route::put('alumnos/{id}', [AlumnoController::class, 'update']); // Actualizar un alumno existente por ID
    Route::delete('alumnos/{id}', [AlumnoController::class, 'destroy']); // Eliminar un alumno por ID
    Route::get('/alumnos-detalles/{id_alumno}', [AlumnoController::class, 'getAlumnosConDetalles']); // Obtener detalles de un alumno específico
    Route::put('/alumnos-baja/{id}', [AlumnoController::class, 'bajaAlumno']); // Dar de baja a un alumno por ID
    Route::put('/alumnos/{id}/alta', [AlumnoController::class, 'altaAlumno']); // Dar de alta a un alumno por ID
    Route::get('/alumnos-combinados/pdf', [AlumnoController::class, 'PDFmostrarDatosCombinados']); // Generar PDF con datos combinados de alumnos
});

// Grupo de rutas para Clases
Route::middleware('api')->group(function () {
    Route::get('clases', [ClaseController::class, 'index']); // Obtener todas las clases
    Route::get('clases/{id}', [ClaseController::class, 'show']); // Obtener una clase específica por ID
    Route::put('clases/{id}', [ClaseController::class, 'update']); // Actualizar una clase existente por ID
    Route::delete('clases/{id}', [ClaseController::class, 'destroy']); // Eliminar una clase por ID
    Route::post('/clases', [ClaseController::class, 'store']); // Crear una nueva clase
    Route::get('/alumnos-clases/{id_programa}/{id_clase?}', [ClaseController::class, 'obtenerDatos']); // Obtener datos de alumnos en clases específicas
    Route::post('/mostrar-informacion-alumno', [ClaseController::class, 'mostrarInformacionAlumno']); // Mostrar información de un alumno específico
    Route::post('/actualizar-beca', [ClaseController::class, 'actualizarBeca']); // Actualizar beca de un alumno
});

// Grupo de rutas para Maestros
Route::middleware('api')->group(function () {
    Route::get('maestros', [MaestroController::class, 'index']); // Obtener todos los maestros
    Route::get('maestros/{id}', [MaestroController::class, 'show']); // Obtener un maestro específico por ID
    Route::post('maestros', [MaestroController::class, 'store']); // Crear un nuevo maestro
    Route::put('maestros/{id}', [MaestroController::class, 'update']); // Actualizar un maestro existente por ID
    Route::delete('maestros/{id}', [MaestroController::class, 'destroy']); // Eliminar un maestro por ID
    Route::get('/lista-maestros', [MaestroController::class, 'obtenerMaestros']); // Obtener una lista de maestros
    Route::put('/maestros-status/{id}', [MaestroController::class, 'actualizarStatus']); // Actualizar el estado de un maestro por ID
});

// Grupo de rutas para Nóminas
Route::middleware('api')->group(function () {
    Route::get('nominas', [NominaController::class, 'index']); // Obtener todas las nóminas
    Route::get('nominas/{id}', [NominaController::class, 'show']); // Obtener una nómina específica por ID
    Route::post('nominas', [NominaController::class, 'store']); // Crear una nueva nómina
    Route::put('nominas/{id}', [NominaController::class, 'update']); // Actualizar una nómina existente por ID
    Route::delete('nominas/{id}', [NominaController::class, 'destroy']); // Eliminar una nómina por ID
    Route::get('/nominas', [NominaController::class, 'mostrarNominas']); // Mostrar todas las nóminas
    Route::get('/nominas/anios', [NominaController::class, 'mostrarAnios']); // Mostrar años de nóminas disponibles
    Route::post('/generar-nomina', [NominaController::class, 'generarNomina']); // Generar una nueva nómina
    Route::get('/informe-nomina/{id_nomina}', [NominaController::class, 'informeNomina']); // Obtener informe de una nómina específica
});

// Grupo de rutas para Adeudos de Programas
Route::middleware('api')->group(function () {
    Route::get('adeudos-programas', [AdeudoProgramaController::class, 'index']); // Obtener todos los adeudos de programas
    Route::post('adeudos-programas', [AdeudoProgramaController::class, 'store']); // Crear un nuevo adeudo de programa
    Route::get('adeudos-programas/{id}', [AdeudoProgramaController::class, 'show']); // Obtener un adeudo de programa específico por ID
    Route::put('adeudos-programas/{id}', [AdeudoProgramaController::class, 'update']); // Actualizar un adeudo de programa existente por ID
    Route::delete('adeudos-programas/{id}', [AdeudoProgramaController::class, 'destroy']); // Eliminar un adeudo de programa por ID
    Route::get('/adeudos-programas/alumno/{id_alumno}', [AdeudoProgramaController::class, 'filterByAlumno']); // Filtrar adeudos por ID de alumno
});

// Grupo de rutas para Cortes
Route::middleware('api')->group(function () {
    Route::get('/cortes', [CorteController::class, 'index']); // Obtener todos los cortes
    Route::get('/corte-caja', [CorteController::class, 'corteCaja']); // Obtener el corte de caja actual
    Route::get('/cortes/{anio}', [CorteController::class, 'getCortesPorAnio']); // Obtener cortes por año
    Route::get('/cortes/{anio}/{mes}', [CorteController::class, 'getCortesPorMes']); // Obtener cortes por mes y año
    Route::get('/info-cortes/{id_corte}', [CorteController::class, 'getPagosPorCorte']); // Obtener pagos por corte específico
    Route::post('/realizar-corte', [CorteController::class, 'realizarCorte']); // Realizar un nuevo corte
    Route::post('/miscelanea', [CorteController::class, 'miscelanea']); // Realizar operación miscelánea
});

// Grupo de rutas para Expediente de Alumnos
Route::middleware('api')->group(function () {
    Route::get('/adeudos/{id}', [ExpedienteAlumnoController::class, 'getAdeudosPorAlumno']); // Obtener adeudos por ID de alumno
    Route::get('/pagos/{id}', [ExpedienteAlumnoController::class, 'getPagosPorAlumno']); // Obtener pagos por ID de alumno
    Route::get('/clases/{id}', [ExpedienteAlumnoController::class, 'getProgramasPorAlumno']); // Obtener programas por ID de alumno
    Route::get('/informacion', [ExpedienteAlumnoController::class, 'obtenerInformacionVisitas']); // Obtener información de visitas
    Route::get('/programas/{id}', [ExpedienteAlumnoController::class, 'obtenerProgramas']); // Obtener programas específicos
    Route::post('/inscripcion/{id}', [ExpedienteAlumnoController::class, 'registrarInscripcion']); // Registrar una inscripción
    Route::post('/registrar-recargo/{id}', [ExpedienteAlumnoController::class, 'registrarRecargo']);
    Route::post('/registrar-visita/{id_alumno}/{id_clase}', [ExpedienteAlumnoController::class, 'registrarVisita']);
    Route::post('/registrar-programa', [ExpedienteAlumnoController::class, 'agregarPrograma']);
    Route::post('/procesar-pagos', [ExpedienteAlumnoController::class, 'accionPago']);

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