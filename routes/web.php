<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReciboController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/recibos/{id}', [ReciboController::class, 'generarRecibo']);

Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
