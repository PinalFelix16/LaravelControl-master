<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Mostrar todos los usuarios
    public function index()
    {
        
    }

    // Mostrar un usuario específico
    public function show($id)
    {
        return User::findOrFail($id);
    }

    // Crear un nuevo usuario
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'rol'      => 'sometimes|string',
        ]);

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        return response()->json($user, 201);
    }

    // Actualizar un usuario existente
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,{$id}",
            'password' => 'sometimes|string|min:6',
            'rol'      => 'sometimes|string',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json($user, 200);
    }

    // Eliminar un usuario
    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(null, 204);
    }

    // Lista simplificada para selects, etc.
    public function listaUsuarios()
    {
        // Ahora consulta la tabla users, no 'usuarios'
        $usuarios = DB::table('users')->select('id', 'name')->get();
        return response()->json($usuarios, 200);
    }

    // Recargos (dejamos igual)
    public function agregarRecargos()
    {
        // ... tu código original de recargos ...
    }
}
