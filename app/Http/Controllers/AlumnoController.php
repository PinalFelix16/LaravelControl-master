<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // ← ¡AGREGA ESTA LÍNEA!
use App\Models\Alumno;

class AlumnoController extends Controller
{
    /**
     * Listar alumnos (opcionalmente filtrando por status)
     */
    public function index(Request $request)
    {

        $status = $request->query('status'); // puede ser 'activo' o 'inactivo'

        $alumnos = Alumno::query()
            ->when($status, fn($q) => $q->where('status', $status))
            ->paginate(10);

        return response()->json($alumnos);
    }

    /**
     * Mostrar un alumno individual (expediente)
     */

    public function show($id)
    {
        $alumno = Alumno::with('clases')->find($id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        return response()->json([
            'id' => $alumno->id,
            'nombre' => $alumno->nombre . ' ' . $alumno->apellido,
            'correo' => $alumno->correo ?? '-',
            'telefono' => $alumno->telefono ?? '-',
            'fecha_nacimiento' => $alumno->fecha_nacimiento ?? '-',
            'status' => $alumno->status ?? '-',
            'clases' => $alumno->clases->pluck('nombre_clase')->toArray() ?? [],
        ]);
    }


    public function destroy($id)
    {
        Alumno::destroy($id);
        return response()->json(null, 204);
    }

    public function mostrarDatosCombinados(Request $request)
    {
        $status = $request->query('status', 1);

        // Ejemplo: traer alumnos con sus clases y adeudos
        $alumnos = Alumno::with(['clases', 'adeudos'])
            ->where('estatus', $status ? 'activo' : 'inactivo')
            ->get();

        return response()->json($alumnos);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'        => 'required|string|max:100',
            'fecha_nac'     => 'nullable|date',
            'celular'       => 'nullable|string|max:20',
            'tutor'         => 'nullable|string|max:100',
            'tutor_2'       => 'nullable|string|max:100',
            'telefono'      => 'nullable|string|max:20',
            'telefono_2'    => 'nullable|string|max:20',
            'hist_medico'   => 'nullable|string|max:255',
            'status'        => 'required|string|max:20',
            'beca'          => 'nullable|string|max:50',
        ], [
            'nombre.required'      => 'El nombre del alumno es obligatorio.',
            'status.required'      => 'El status es obligatorio.',
            'fecha_nac.date'       => 'La fecha de nacimiento debe tener formato válido.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Hay errores en los datos enviados.',
            ], 422);
        }

        $alumno = Alumno::create($validator->validated());

        return response()->json([
            'data' => $alumno,
            'message' => 'Alumno registrado correctamente.'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json([
                'message' => 'Alumno no encontrado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre'        => 'sometimes|required|string|max:100',
            'fecha_nac'     => 'nullable|date',
            'celular'       => 'nullable|string|max:20',
            'tutor'         => 'nullable|string|max:100',
            'tutor_2'       => 'nullable|string|max:100',
            'telefono'      => 'nullable|string|max:20',
            'telefono_2'    => 'nullable|string|max:20',
            'hist_medico'   => 'nullable|string|max:255',
            'status'        => 'sometimes|required|string|max:20',
            'beca'          => 'nullable|string|max:50',
        ], [
            'nombre.required'      => 'El nombre del alumno es obligatorio.',
            'status.required'      => 'El status es obligatorio.',
            'fecha_nac.date'       => 'La fecha de nacimiento debe tener formato válido.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'message' => 'Hay errores en los datos enviados.'
            ], 422);
        }

        $alumno->update($validator->validated());

        return response()->json([
            'data' => $alumno,
            'message' => 'Alumno actualizado correctamente.'
        ]);
    }
}
