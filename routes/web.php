<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReciboController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/recibos/{id}', [ReciboController::class, 'generarRecibo']);
