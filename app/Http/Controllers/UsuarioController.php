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

    public function update(Request $request, $id)
{
    $usuario = Usuario::findOrFail($id);

    // Solo superadmin puede editar permisos y usuario
    if ($request->user()->permisos !== 'SUPERADMINISTRADOR' && $request->has('permisos')) {
        return response()->json(['message' => 'Solo superadmin puede cambiar permisos.'], 403);
    }

    $request->validate([
        'nombre' => 'string|max:100',
        'password' => 'nullable|string|min:6',
        'permisos' => 'string|max:50'
    ]);

    if ($request->nombre)   $usuario->nombre = $request->nombre;
    if ($request->password) $usuario->password = Hash::make($request->password);
    if ($request->permisos && $request->user()->permisos === 'SUPERADMINISTRADOR')
        $usuario->permisos = $request->permisos;

    $usuario->save();

    return response()->json(['usuario' => $usuario, 'message' => 'Usuario actualizado correctamente']);
}

public function destroy(Request $request, $id)
{
    // Solo el superadmin puede eliminar
    if ($request->user()->permisos !== 'SUPERADMINISTRADOR') {
        return response()->json(['message' => 'Solo el superadmin puede eliminar usuarios.'], 403);
    }
    $usuario = Usuario::findOrFail($id);
    $usuario->delete();

    return response()->json(['message' => 'Usuario eliminado correctamente']);
}

public function index(Request $request)
{
    // Puedes poner validación para que solo el superadmin vea la lista si quieres
    $usuarios = Usuario::all();
    return response()->json($usuarios);
}


}
