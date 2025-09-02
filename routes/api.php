<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\MaestroController;
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
use App\Http\Controllers\CorteController;
use App\Http\Controllers\NominaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Todas las rutas están debajo de /api automáticamente.
*/

// ---------- AUTH ----------
Route::post('login',  [AuthController::class, 'login']);

// ---------- ALUMNOS ----------
Route::get('alumnos',                  [AlumnoController::class, 'index']);
Route::post('alumnos',                 [AlumnoController::class, 'store']);
Route::get('alumnos/datos-combinados', [AlumnoController::class, 'datosCombinados']);
Route::get('alumnos/{id}/expediente',  [AlumnoController::class, 'expediente'])->whereNumber('id');
Route::put('alumnos/{id}/expediente',  [AlumnoController::class, 'actualizarExpediente'])->whereNumber('id');
Route::put('alumnos/{id}',             [AlumnoController::class, 'update'])->whereNumber('id');
Route::patch('alumnos/{id}',           [AlumnoController::class, 'update'])->whereNumber('id');

Route::get('pagos/{id_alumno}', [PagoController::class, 'byAlumno'])->whereNumber('id_alumno');

// ---------- CLASES ----------
// LISTA pública (forzando MySQL)
Route::get('clases', function () {
    $db = DB::connection('mysql'); // <-- fuerza MySQL
    return $db->table('clases as c')
        ->leftJoin('programas_predefinidos as p', 'p.id_programa', '=', 'c.id_programa')
        ->leftJoin('maestros as m', 'm.id_maestro', '=', 'c.id_maestro')
        ->select([
            'c.id_clase','c.id_programa','c.alumno_id','c.nombre','c.id_maestro',
            'c.informacion','c.lugar','c.hora_inicio','c.hora_fin','c.dias',
            'c.mensualidad','c.complejo','c.porcentaje','c.personal',
            DB::raw('COALESCE(p.nombre,"")  as programa_nombre'),
            DB::raw('COALESCE(m.nombre,"") as nombre_maestro'),
        ])
        ->orderBy('c.id_clase','desc')
        ->get();
});

// DETALLE público (para el editor)
Route::get('clases/{id}', function ($id) {
    $db = DB::connection('mysql'); // <-- fuerza MySQL
    $row = $db->table('clases as c')
        ->leftJoin('programas_predefinidos as p', 'p.id_programa', '=', 'c.id_programa')
        ->leftJoin('maestros as m', 'm.id_maestro', '=', 'c.id_maestro')
        ->select([
            'c.id_clase','c.id_programa','c.alumno_id','c.nombre','c.id_maestro',
            'c.informacion','c.lugar','c.hora_inicio','c.hora_fin','c.dias',
            'c.mensualidad','c.complejo','c.porcentaje','c.personal',
            DB::raw('COALESCE(p.nombre,"")  as programa_nombre'),
            DB::raw('COALESCE(m.nombre,"") as nombre_maestro'),
        ])
        ->where('c.id_clase', $id)
        ->first();

    return $row ? response()->json($row) : response()->json(['message'=>'Clase no encontrada'], 404);
})->whereNumber('id');

// Modificaciones con token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('clases',        [ClaseController::class, 'store']);
    Route::put('clases/{id}',    [ClaseController::class, 'update'])->whereNumber('id');
    Route::delete('clases/{id}', [ClaseController::class, 'destroy'])->whereNumber('id');

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
// Nueva ruta para obtener pagos por alumno
Route::get('alumnos/{id_alumno}/pagos', [PagoController::class, 'byAlumno'])
    ->whereNumber('id_alumno');

Route::apiResource('pagos', App\Http\Controllers\PagoController::class);
Route::get('pagos/{id}/imprimir', [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::get('pagos/{id}/recibo-pdf', [App\Http\Controllers\PagoController::class, 'generarReciboPDF']);

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
// ---------- MAESTROS ----------
// LISTA pública (forzando MySQL)
Route::get('maestros', function () {
    return DB::connection('mysql')->table('maestros')
        ->select(['id_maestro','nombre'])
        ->orderBy('nombre')
        ->get();
});
// API resource SIN index para no chocar con la lista anterior
Route::apiResource('maestros', MaestroController::class)->except(['index']);

// ---------- PROGRAMAS ----------
// LISTA pública (forzando MySQL)
Route::get('programas', function () {
    return DB::connection('mysql')->table('programas_predefinidos')
        ->select(['id_programa','nombre','mensualidad','nivel','complex','status','ocultar'])
        ->orderByDesc('id_programa')
        ->get();
});
// API resource SIN index
Route::apiResource('programas', ProgramaController::class)->except(['index']);

// Crear programa + clases (tus alias)
Route::post('programas-with-clases', [ProgramaController::class, 'storeWithClases'])->name('programas.withClases');
Route::post('programas/with-clases', [ProgramaController::class, 'storeWithClases'])->name('programas.withClases.slash');
Route::post('pgwc',                  [ProgramaController::class, 'storeWithClases'])->name('pgwc');

// ---------- PING / FALLBACK ----------
Route::get('ping', fn() => response()->json(['ok' => true, 'base_path' => base_path()]));
Route::fallback(fn() => response()->json(['message' => 'Ruta no encontrada.'], 404));

// ---------- RESTO DE APIs (como las tenías) ----------
Route::apiResource('pagos', PagoController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('becas', BecaController::class);
Route::apiResource('descuentos', DescuentoController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('pagos-programas', PagoProgramaController::class);
Route::apiResource('pagos-fragmentados', PagoFragmentadoController::class);
Route::apiResource('pagos-secundarios', PagoSecundarioController::class);
Route::apiResource('adeudos-programas', AdeudoProgramaController::class);
Route::apiResource('adeudos-fragmentados', AdeudoFragmentadoController::class);
Route::apiResource('adeudos-secundarios', AdeudoSecundarioController::class);
Route::apiResource('miscelanea', MiscelaneaController::class);

// Cortes
Route::get('corte-caja', [CorteController::class, 'corteCaja']);
Route::post('realizar-corte', [CorteController::class, 'realizarCorte']);
Route::get('cortes', [CorteController::class, 'mensual']);
Route::get('cortes/historico/{anio}', [CorteController::class, 'getCortesPorAnio']);
Route::get('cortes/historico/{anio}/{mes}', [CorteController::class, 'getCortesPorMes']);
Route::get('cortes/por-semana', [CorteController::class, 'porSemana']);
Route::get('cortes/{id_corte}', [CorteController::class, 'show']);
Route::get('cortes/{id_corte}/movimientos', [CorteController::class, 'getPagosPorCorte']);
Route::prefix('reportes/cortes')->group(function () {
    Route::get('por-semana', [CorteController::class, 'reporteSemana']);
    Route::get('historico/{anio}/{mes}', [CorteController::class, 'reporteMes'])->whereNumber('anio')->whereNumber('mes');
    Route::get('historico/{anio}', [CorteController::class, 'reporteAnio'])->whereNumber('anio');
    Route::get('{id}', [CorteController::class, 'reporteDetalle'])->whereNumber('id');
});

// Nómina
Route::get('nominas/anios',            [NominaController::class, 'mostrarAnios']);
Route::get('nominas/mostrar/{anio}',   [NominaController::class, 'mostrarNominas'])->whereNumber('anio');
Route::post('nominas/generar',         [NominaController::class, 'generarNomina']);
Route::get('nominas/{id}/informe',       [NominaController::class, 'informeNomina']);
Route::get('nominas/{id}/informe/print', [NominaController::class, 'informeNominaPrint']);
Route::get('nominas/{id}/informe/pdf',   [NominaController::class, 'informeNominaPdf']);
Route::apiResource('nominas', NominaController::class);

// --- RUTA FAKE PARA EVITAR ERROR DE LOGIN EN SANCTUM ---
Route::get('/login', fn() => response()->json(['error' => 'No autenticado'], 401))->name('login');
