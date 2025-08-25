<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clase;

class ClaseController extends Controller
{
    public function index(Request $request)
{
    // con with (dos queries, limpio):
    $clases = Clase::with('programa:id_programa,nombre,mensualidad')
        ->orderBy((new Clase)->getKeyName(), 'asc')
        ->get();

    // Si quieres que el JSON ya lleve un campo plano:
    $clases->transform(function($c){
        $c->programa_nombre = optional($c->programa)->nombre;
        $c->programa_mensualidad = optional($c->programa)->mensualidad;
        return $c;
    });

    return response()->json($clases);
}


public function show($id)
{
    $clase = \App\Models\Clase::with('programa:id_programa,nombre,mensualidad')->find($id);

    if (!$clase) {
        return response()->json(['message' => 'Clase no encontrada'], 404);
    }

    // Campos auxiliares para el front (aunque no haya programa)
    $clase->programa_nombre      = optional($clase->programa)->nombre;
    $clase->programa_mensualidad = optional($clase->programa)->mensualidad;

    return response()->json($clase);
}




    public function store(Request $request)
    {
        $data = $request->validate([
            'id_programa' => 'required|integer',
            'alumno_id'   => 'nullable|integer',
            'nombre'      => 'required|string|max:30',
            'id_maestro'  => 'required|string|max:6',
            'id_maestro_2'=> 'nullable|string|max:6',
            'informacion' => 'nullable|string|max:100',
            'lugar'       => 'nullable|string|max:100',
            'hora_inicio' => 'nullable|date_format:H:i',
            'hora_fin'    => 'nullable|date_format:H:i',
            'dias'        => 'nullable', // array|string
            'mensualidad' => 'nullable|numeric',
            'complejo'    => 'nullable|in:0,1',
            'porcentaje'  => 'required|numeric',
            'personal'    => 'required|integer',
        ]);

        // Normaliza default
        $data['alumno_id'] = $data['alumno_id'] ?? 0;
        $data['complejo']  = isset($data['complejo']) ? (int)$data['complejo'] : 0;

        $clase = Clase::create($data);
        return response()->json($clase, 201);
    }

    public function update(Request $request, $id)
    {
        $clase = Clase::find($id);
        if (!$clase) {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }

        $data = $request->validate([
            'id_programa' => 'sometimes|integer',
            'alumno_id'   => 'sometimes|integer|nullable',
            'nombre'      => 'sometimes|string|max:30',
            'id_maestro'  => 'sometimes|string|max:6',
            'id_maestro_2'=> 'sometimes|string|max:6|nullable',
            'informacion' => 'sometimes|string|max:100|nullable',
            'lugar'       => 'sometimes|string|max:100|nullable',
            'hora_inicio' => 'sometimes|date_format:H:i|nullable',
            'hora_fin'    => 'sometimes|date_format:H:i|nullable',
            'dias'        => 'sometimes|nullable', // array|string
            'mensualidad' => 'sometimes|numeric|nullable',
            'complejo'    => 'sometimes|in:0,1|nullable',
            'porcentaje'  => 'sometimes|numeric|nullable',
            'personal'    => 'sometimes|integer|nullable',
        ]);

        $clase->fill($data)->save();

        return response()->json($clase);
    }

    public function destroy($id)
    {
        $clase = Clase::find($id);
        if (!$clase) {
            return response()->json(['message' => 'Clase no encontrada'], 404);
        }
        $clase->delete();
        return response()->json(['message' => 'Clase eliminada']);
    }
}
