<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ========================
// 🔹 Autenticación
// ========================
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);

// ========================
// 🔹 Rutas personalizadas de alumnos (ARRIBA del apiResource)
// ========================
Route::get('alumnos/datos-combinados', [App\Http\Controllers\AlumnoController::class, 'datosCombinados']);
Route::get('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'expediente'])->where('id', '[0-9]+');
Route::put('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'actualizarExpediente'])->where('id', '[0-9]+');
Route::post('alumnos/{id}/beca', [App\Http\Controllers\AlumnoController::class, 'asignarBeca'])->where('id', '[0-9]+');
Route::post('alumnos/{id}/descuento', [App\Http\Controllers\AlumnoController::class, 'asignarDescuento'])->where('id', '[0-9]+');

// 🔹 Alta/Baja de alumnos
Route::put('alumnos/{id}/baja', [App\Http\Controllers\AlumnoController::class, 'bajaAlumno'])->where('id', '[0-9]+');
Route::put('alumnos/{id}/alta', [App\Http\Controllers\AlumnoController::class, 'altaAlumno'])->where('id', '[0-9]+');

// ========================
// 🔹 Recursos principales (CRUD RESTful)
// ========================
Route::apiResource('alumnos', App\Http\Controllers\AlumnoController::class)->where(['alumno' => '[0-9]+']);
Route::apiResource('maestros', App\Http\Controllers\MaestroController::class);
Route::apiResource('clases', App\Http\Controllers\ClaseController::class);
Route::apiResource('programas', App\Http\Controllers\ProgramaController::class);
Route::apiResource('pagos', App\Http\Controllers\PagoController::class);
Route::apiResource('usuarios', App\Http\Controllers\UserController::class);
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

// ========================
// 🔹 Rutas personalizadas de maestros/pagos
// ========================
Route::get('maestros/{id}/informe', [App\Http\Controllers\MaestroController::class, 'informe']);
Route::get('pagos/{id}/imprimir', [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::get('pagos/{id}/recibo-pdf', [App\Http\Controllers\PagoController::class, 'generarReciboPDF']);
Route::get('adeudos/exportar-pdf', [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);

// ========================
// 🔹 Rutas protegidas con Sanctum
// ========================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

    // Usuarios protegidos
    Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'index']);
    Route::post('/usuarios', [App\Http\Controllers\UsuarioController::class, 'store']);
    Route::put('/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [App\Http\Controllers\UsuarioController::class, 'destroy']);
});
