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

// LISTAR
Route::get('alumnos', [AlumnoController::class, 'index']);

// CREAR   (resuelve tu 405 al guardar)
Route::post('alumnos', [AlumnoController::class, 'store']);

// EDITAR
Route::put('alumnos/{id}',   [AlumnoController::class, 'update'])->whereNumber('id');
Route::patch('alumnos/{id}', [AlumnoController::class, 'update'])->whereNumber('id');
// ---------- CLASES ----------

Route::middleware('auth:sanctum')->group(function () {
    Route::post('clases',      [ClaseController::class, 'store']);
    Route::put('clases/{id}',  [ClaseController::class, 'update'])->whereNumber('id');
    Route::delete('clases/{id}', [ClaseController::class, 'destroy'])->whereNumber('id');
});

// (Opcional) Fallback JSON para 404 en API
Route::fallback(function () {
    return response()->json(['message' => 'Ruta no encontrada.'], 404);
});
