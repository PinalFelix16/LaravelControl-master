<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    // Endpoint para crear usuario (solo superadmin)
    public function store(Request $request)
    {
        // Verifica si el usuario autenticado es superadmin
        if ($request->user()->permisos !== 'SUPERADMINISTRADOR') {
            return response()->json(['message' => 'No tienes permisos para crear usuarios'], 403);
        }

        // Validación de los campos
        $request->validate([
            'usuario' => 'required|unique:usuarios,usuario',
            'nombre' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'permisos' => 'required|string|max:50'
        ]);

        // Crear usuario encriptando la contraseña
        $usuario = Usuario::create([
            'usuario' => $request->usuario,
            'nombre' => $request->nombre,
            'password' => Hash::make($request->password),
            'permisos' => $request->permisos,
        ]);

        return response()->json(['usuario' => $usuario, 'message' => 'Usuario creado correctamente'], 201);
    }
}
