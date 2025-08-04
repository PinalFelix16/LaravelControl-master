<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clase;

class ClaseController extends Controller
{
    // Listar todas las clases
    public function index()
    {
        return response()->json(Clase::all());
    }

    // Crear una nueva clase
    public function store(Request $request)
    {
        $request->validate([
            'id_programa' => 'required|integer',
            'alumno_id'   => 'required|integer',
            'nombre'      => 'required|string|max:30',
            'id_maestro'  => 'required|string|max:6',
            'informacion' => 'nullable|string|max:100',
            'porcentaje'  => 'nullable|numeric',
            'personal'    => 'nullable|integer',
        ]);

        $clase = Clase::create($request->all());
        return response()->json($clase, 201);
    }

    // Ver una clase específica
    public function show($id)
    {
        $clase = Clase::findOrFail($id);
        return response()->json($clase);
    }

    // Actualizar clase
    public function update(Request $request, $id)
{
    $clase = Clase::findOrFail($id);

    // Asigna todos los campos según la base de datos
    $clase->id_programa = $request->input('id_programa');
    $clase->alumno_id = $request->input('alumno_id');
    $clase->nombre_clase = $request->input('nombre_clase');
    $clase->id_maestro = $request->input('id_maestro');
    $clase->informacion = $request->input('informacion');
    $clase->porcentaje = $request->input('porcentaje');
    $clase->personal = $request->input('personal');

    $clase->save();
    // Opcional: retorna el objeto actualizado
    return response()->json($clase);
}



    // Borrar clase
    public function destroy($id)
    {
        $clase = Clase::findOrFail($id);
        $clase->delete();

        return response()->json(['message' => 'Clase eliminada correctamente']);
    }
}
