<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller

{
    /**
     * LISTAR ALUMNOS
     * Admite ?status=activo | inactivo | 1 | 0 (los 1/0 se normalizan).
     * Retorna un arreglo simple (no paginado) y ALIAS "id" para la UI.
     */
    public function index(Request $request)
    {
        // Normaliza el status si viene 1/0
        $status = $request->query('status');
        if ($status === '1' || $status === 1)  $status = 'activo';
        if ($status === '0' || $status === 0)  $status = 'inactivo';

        $q = Alumno::query();

        if ($status === 'activo' || $status === 'inactivo') {
            $q->where('status', $status);
        }

        // Alias "id_alumno AS id" para que el frontend use alumno.id sin cambios
        $alumnos = $q->select([
                'id_alumno as id',
                'id_alumno',
                'nombre',
                'apellido',
                'correo',
                'celular',
                'telefono',
                'fecha_nacimiento',
                'status',
                'tutor',
                'tutor_2',
                'telefono_2',
                'hist_medico',
                'beca',
                'created_at',
                'updated_at',
            ])
            ->get();

        return response()->json($alumnos, 200);
    }

    /**
     * OBTENER UN ALUMNO POR ID (id_alumno)
     * Gracias a que el modelo tiene primaryKey = id_alumno, find($id) funciona.
     */
    public function show($id)
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        // Opcional: asegurar el alias "id" en la respuesta individual
        $alumno->setAttribute('id', $alumno->id_alumno);

        return response()->json($alumno, 200);
    }

    /**
     * CREAR ALUMNO
     * Valida y crea. Acepta status 'activo' | 'inactivo'.
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nombre'           => 'required|string|max:255',
            'apellido'         => 'required|string|max:255',
            'correo'           => 'nullable|email|max:255',
            'celular'          => 'nullable|string|max:10',
            'telefono'         => 'nullable|string|max:10',
            'fecha_nacimiento' => 'required|date',
            'tutor'            => 'nullable|string|max:45',
            'tutor_2'          => 'nullable|string|max:45',
            'telefono_2'       => 'nullable|string|max:10',
            'hist_medico'      => 'nullable|string|max:250',
            'status'           => 'required|in:activo,inactivo',
            'beca'             => 'nullable|numeric|min:0',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $alumno = Alumno::create($v->validated());

        // Alias "id" para la respuesta
        $alumno->setAttribute('id', $alumno->id_alumno);

        return response()->json($alumno, 201);
    }

    /**
     * ACTUALIZAR ALUMNO
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        $v = Validator::make($request->all(), [
            'nombre'           => 'sometimes|required|string|max:255',
            'apellido'         => 'sometimes|required|string|max:255',
            'correo'           => 'sometimes|nullable|email|max:255',
            'celular'          => 'sometimes|nullable|string|max:10',
            'telefono'         => 'sometimes|nullable|string|max:10',
            'fecha_nacimiento' => 'sometimes|required|date',
            'tutor'            => 'sometimes|nullable|string|max:45',
            'tutor_2'          => 'sometimes|nullable|string|max:45',
            'telefono_2'       => 'sometimes|nullable|string|max:10',
            'hist_medico'      => 'sometimes|nullable|string|max:250',
            'status'           => 'sometimes|required|in:activo,inactivo',
            'beca'             => 'sometimes|nullable|numeric|min:0',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $alumno->fill($v->validated())->save();

        // Alias "id" para la respuesta
        $alumno->setAttribute('id', $alumno->id_alumno);

        return response()->json($alumno, 200);
    }

    /**
     * ELIMINAR ALUMNO
     */
    public function destroy($id)
    {
        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        $alumno->delete();

        return response()->json(['message' => 'Alumno eliminado'], 200);
    }
}
