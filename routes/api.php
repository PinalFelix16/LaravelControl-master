<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


// AUTENTICACIÓN
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);

// CRUD PRINCIPALES Y ACCIONES
Route::apiResource('alumnos', App\Http\Controllers\AlumnoController::class);
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
Route::get('/adeudos/exportar-pdf', [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/usuarios', [App\Http\Controllers\UsuarioController::class, 'store']);



// ... otras rutas públicas (login, registro, etc.)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/adeudos/exportar-pdf', [App\Http\Controllers\AdeudoProgramaController::class, 'exportarPDF']);});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/usuarios', [App\Http\Controllers\UsuarioController::class, 'index']);
    Route::post('/usuarios', [App\Http\Controllers\UsuarioController::class, 'store']);
});



Route::get('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'expediente']);
Route::put('alumnos/{id}/expediente', [App\Http\Controllers\AlumnoController::class, 'actualizarExpediente']);
Route::post('alumnos/{id}/beca', [App\Http\Controllers\AlumnoController::class, 'asignarBeca']);
Route::post('alumnos/{id}/descuento', [App\Http\Controllers\AlumnoController::class, 'asignarDescuento']);
Route::get('maestros/{id}/informe', [App\Http\Controllers\MaestroController::class, 'informe']);
Route::get('pagos/{id}/imprimir', [App\Http\Controllers\PagoController::class, 'imprimirRecibo']);
Route::apiResource('miscelanea', App\Http\Controllers\MiscelaneaController::class);

// Elimina/comenta cualquier cierre de llave '})' suelta que no tenga apertura de grupo arriba.


# Route::get('/user', function (Request $request) {
#     return $request->user();
# })->middleware('auth:sanctum');


# Route::get('/user', function (Request $request) {
#     return $request->user();
# })->middleware('auth:sanctum');
