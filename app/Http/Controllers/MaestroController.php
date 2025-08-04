<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use Illuminate\Http\Request;

class MaestroController extends Controller
{

    public $timestamps = false;
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
            'status' => 'required|integer|in:0,1'
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

    $request->validate([
        'nombre' => 'sometimes|required|string|max:255',
        'nombre_titular' => 'nullable|string|max:255',
        'direccion' => 'nullable|string',
        'fecha_nac' => 'nullable|date',
        'rfc' => 'nullable|string|max:20',
        'celular' => 'nullable|string|max:20',
        'status' => 'sometimes|required|integer|in:0,1'
    ]);

    $maestro->update($request->all());
    return response()->json($maestro);
    }

    // Eliminar un maestro
    public function destroy($id)
    {
    $maestro = Maestro::find($id);
    if (!$maestro) {
        return response()->json(['message' => 'Maestro no encontrado.'], 404);
    }
    $maestro->delete();
    return response()->json(null, 204);
    }

}
