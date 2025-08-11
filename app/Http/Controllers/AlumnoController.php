<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    /**
     * GET /api/alumnos?status=0|1
     * Lista alumnos (opcionalmente filtrados por status 0/1)
     */
    public function index(Request $request)
    {
        $q = Alumno::query();

        if ($request->has('status')) {
            $s = (int) $request->query('status'); // fuerza 0/1
            $q->where('status', $s);
        }

        return response()->json($q->orderBy('id_alumno', 'asc')->get());
    }

    /**
     * GET /api/alumnos/datos-combinados?status=0|1
     * Devuelve alumnos + conteo de clases (clases_count)
     * Requiere que el modelo Alumno tenga ->clases()
     */
   public function datosCombinados(Request $request)
{
    $q = \App\Models\Alumno::query()
        ->select('alumnos.*')
        ->withCount('clases');

    if ($request->has('status')) {
        $q->where('status', (int) $request->query('status')); // <-- FILTRO
    }

    return response()->json($q->orderBy('id_alumno', 'asc')->get());
}

    /**
     * GET /api/alumnos/{id}
     */
    public function show($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }
        return response()->json($alumno);
    }

    /**
     * POST /api/alumnos
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nombre'            => 'required|string|min:2',
            'apellido'          => 'nullable|string',
            'correo'            => 'nullable|email|unique:alumnos,correo',
            'celular'           => 'nullable|string|max:50',
            'telefono'          => 'nullable|string|max:50',
            'fecha_nacimiento'  => 'nullable|date',
            'tutor'             => 'nullable|string|max:255',
            'tutor_2'           => 'nullable|string|max:255',
            'telefono_2'        => 'nullable|string|max:50',
            'hist_medico'       => 'nullable|string',
            'beca'              => 'nullable|string|max:100',
            'status'            => 'nullable|boolean', // 0/1
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $data = $v->validated();
        if (!array_key_exists('status', $data)) {
            $data['status'] = 1; // por defecto activo
        }

        $alumno = Alumno::create($data);
        return response()->json($alumno, 201);
    }

    /**
     * PUT /api/alumnos/{id}
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        $v = Validator::make($request->all(), [
            'nombre'            => 'sometimes|required|string|min:2',
            'apellido'          => 'nullable|string',
            'correo'            => 'nullable|email|unique:alumnos,correo,' . $id . ',id_alumno',
            'celular'           => 'nullable|string|max:50',
            'telefono'          => 'nullable|string|max:50',
            'fecha_nacimiento'  => 'nullable|date',
            'tutor'             => 'nullable|string|max:255',
            'tutor_2'           => 'nullable|string|max:255',
            'telefono_2'        => 'nullable|string|max:50',
            'hist_medico'       => 'nullable|string',
            'beca'              => 'nullable|string|max:100',
            'status'            => 'nullable|boolean', // 0/1
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $alumno->update($v->validated());
        return response()->json($alumno);
    }

    /**
     * DELETE /api/alumnos/{id}
     */
    public function destroy($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }
        $alumno->delete();
        return response()->json(['success' => true]);
    }

    /**
     * PUT /api/alumnos/{id}/alta   (ruta personalizada)
     * Tus rutas usan altaAlumno/bajaAlumno, así que dejamos ambos nombres.
     */
    public function altaAlumno($id) { return $this->alta($id); }
    public function bajaAlumno($id) { return $this->baja($id); }

    // Implementación real de alta/baja
    public function alta($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

        $alumno->update(['status' => 1]);
        return response()->json(['success' => true, 'message' => 'Alumno dado de alta correctamente']);
    }

    public function baja($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

        $alumno->update(['status' => 0]);
        return response()->json(['success' => true, 'message' => 'Alumno dado de baja correctamente']);
    }

    /**
     * (Opcional) Si usas expediente y actualizaciones
     * GET /api/alumnos/{id}/expediente
     * PUT /api/alumnos/{id}/expediente
     */
    public function expediente($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

        // agrega aquí lo que necesites retornar (relaciones, etc.)
        return response()->json($alumno);
    }

    public function actualizarExpediente(Request $request, $id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

        $alumno->update($request->all());
        return response()->json(['success' => true, 'message' => 'Expediente actualizado']);
    }
}
