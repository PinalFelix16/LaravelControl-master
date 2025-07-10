<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use Illuminate\Http\Request;

class ClaseController extends Controller
{
    // Listar todas las clases
    public function index()
    {
        return response()->json(Clase::with('alumno')->get());
    }

    // Crear una nueva clase
    public function store(Request $request)
    {
        $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'nombre_clase' => 'required|string|max:255',
            'nivel' => 'required|string|max:255',
            'grupo' => 'required|string|max:255',
            'turno' => 'nullable|string|max:255',
            'costo' => 'required|numeric',
            'estatus' => 'nullable|string'
        ]);

        $clase = Clase::create($request->all());

        return response()->json($clase, 201);
    }

    // Mostrar una clase por ID
    public function show($id)
    {
        $clase = Clase::with('alumno')->find($id);

        if (!$clase) {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }

        return response()->json($clase);
    }

    // Actualizar una clase
    public function update(Request $request, $id)
    {
        $clase = Clase::find($id);

        if (!$clase) {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }

        $clase->update($request->all());

        return response()->json($clase);
    }

    // Eliminar una clase
    public function destroy($id)
    {
        $clase = Clase::find($id);

        if (!$clase) {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }

        $clase->delete();

        return response()->json(['message' => 'Clase eliminada correctamente']);
    }
}
