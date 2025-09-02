<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clase;

class ClaseController extends Controller
{
    public function index()
    {
        return response()->json(Clase::all());
    }

    public function store(Request $request)
    {
        // Validación alineada a tu tabla
        $data = $request->validate([
            'id_programa' => 'required|integer',
            'alumno_id'   => 'required|integer',
            'nombre'      => 'required|string|max:60',  // en BD es 'nombre'
            'id_maestro'  => 'required|string|max:6',
            'informacion' => 'nullable|string|max:100',
            'porcentaje'  => 'nullable|numeric',
            'personal'    => 'nullable|integer',
        ]);

        // Defaults para columnas NOT NULL
        if (!array_key_exists('informacion', $data) || $data['informacion'] === null) {
            $data['informacion'] = '';
        }
        if (!array_key_exists('porcentaje', $data) || $data['porcentaje'] === null) {
            $data['porcentaje'] = 0;
        }
        if (!array_key_exists('personal', $data) || $data['personal'] === null) {
            $data['personal'] = 0;
        }

        $clase = Clase::create($data);
        return response()->json($clase, 201);
    }
    public function byAlumno($id_alumno)
{
    $pagos = \App\Models\Pago::query()
        ->with('alumno') // opcional, si tienes la relación en el modelo Pago
        ->where('id_alumno', $id_alumno) // cambia si la FK tiene otro nombre
        ->orderByDesc('fecha') // ajusta si tu campo de fecha se llama distinto
        ->get();

    return response()->json($pagos);
}


    public function show($id)
    {
        $clase = Clase::findOrFail($id);
        return response()->json($clase);
    }

    public function update(Request $request, $id)
    {
        $clase = Clase::findOrFail($id);

        $data = $request->validate([
            'id_programa' => 'sometimes|required|integer',
            'alumno_id'   => 'sometimes|required|integer',
            'nombre'      => 'sometimes|required|string|max:60', // en BD es 'nombre'
            'id_maestro'  => 'sometimes|required|string|max:6',
            'informacion' => 'nullable|string|max:100',
            'porcentaje'  => 'nullable|numeric',
            'personal'    => 'nullable|integer',
        ]);

        // Defaults para NOT NULL
        if (array_key_exists('informacion', $data) && $data['informacion'] === null) {
            $data['informacion'] = '';
        }
        if (array_key_exists('porcentaje', $data) && $data['porcentaje'] === null) {
            $data['porcentaje'] = 0;
        }
        if (array_key_exists('personal', $data) && $data['personal'] === null) {
            $data['personal'] = 0;
        }

        $clase->fill($data)->save();
        return response()->json($clase);
    }

    public function destroy($id)
    {
        $clase = Clase::findOrFail($id);
        $clase->delete();
        return response()->json(['message' => 'Clase eliminada correctamente']);
    }
}
