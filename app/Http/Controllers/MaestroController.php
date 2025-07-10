<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use Illuminate\Http\Request;

class MaestroController extends Controller
{
    // Listar todos los maestros
    public function index()
    {
        return response()->json(Maestro::all());
    }

    // Crear un nuevo maestro
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_titular' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'fecha_nac' => 'nullable|date',
            'rfc' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'status' => 'nullable|string'
        ]);

        $maestro = Maestro::create($request->all());
        return response()->json($maestro, 201);
    }

    // Mostrar un maestro específico
    public function show($id)
    {
        $maestro = Maestro::findOrFail($id);
        return response()->json($maestro);
    }

    // Actualizar un maestro
    public function update(Request $request, $id)
    {
        $maestro = Maestro::findOrFail($id);
        $maestro->update($request->all());
        return response()->json($maestro);
    }

    // Eliminar un maestro
    public function destroy($id)
    {
        Maestro::destroy($id);
        return response()->json(null, 204);
    }
}
