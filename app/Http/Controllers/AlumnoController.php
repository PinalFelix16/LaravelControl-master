<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // ← ¡AGREGA ESTA LÍNEA!
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function index()
    {
        return Alumno::all();
    }

    public function show($id)
    {
        return Alumno::findOrFail($id);
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
