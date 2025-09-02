<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clase;

class ClaseController extends Controller
{
    // GET /api/clases
    public function index(Request $request)
    {
        try {
            $q = Clase::query();

            if ($request->has('status')) {
                $q->where('status', (int) $request->query('status'));
            }

            // Ordenar por la PK real del modelo (evita errores con 'id' vs 'id_clase')
            $pk = (new Clase)->getKeyName();

            // Descomenta si necesitas relaciones:
            // $q->with(['alumno']);

            return response()->json($q->orderBy($pk, 'asc')->get());
        } catch (\Throwable $e) {
            // Respuesta visible para depurar desde el front durante desarrollo
            return response()->json([
                'message' => 'Error listando clases',
                'error'   => $e->getMessage(),
            ], 500);
        }
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


    // GET /api/clases/{id}
    public function show($id)
    {
        $clase = Clase::findOrFail($id);
        return response()->json($clase);
    }

    // POST /api/clases
    public function store(Request $request)
    {
        $data = $request->validate([
            // 'alumno_id' => 'required|integer|exists:alumnos,id_alumno',
            // 'materia'   => 'required|string|max:255',
            // 'status'    => 'required|in:0,1',
        ]);

        $clase = Clase::create($data);
        return response()->json($clase, 201);
    }

    // PUT /api/clases/{id}
    public function update(Request $request, $id)
    {
        $clase = Clase::findOrFail($id);

        $data = $request->validate([
            // reglas nullable para edición
        ]);

        $clase->update($data);
        return response()->json($clase);
    }

    // DELETE /api/clases/{id}
    public function destroy($id)
    {
        $clase = Clase::findOrFail($id);
        $clase->delete();
        return response()->json(['message' => 'Clase eliminada']);
    }
}
