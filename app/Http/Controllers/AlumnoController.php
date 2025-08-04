<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    /**
     * Registrar un nuevo alumno
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'correo' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $alumno = Alumno::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'status' => 'activo',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alumno registrado correctamente',
            'alumno' => $alumno
        ], 201);
    }

    /**
     * Actualizar un alumno
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::find($id);

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        $alumno->update($request->only([
            'nombre',
            'apellido',
            'correo',
            'telefono',
            'fecha_nacimiento',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Alumno actualizado correctamente',
            'alumno' => $alumno
        ]);
    }

    /**
     * Dar de baja (baja lógica cambiando status)
     */

public function bajaAlumno($id)
{
    $alumno = Alumno::find($id);

    if (!$alumno) {
        return response()->json(['error' => 'Alumno no encontrado'], 404);
    }

    $alumno->update(['status' => 'inactivo']);

    return response()->json([
        'success' => true,
        'message' => 'Alumno dado de baja correctamente',
        'alumno' => $alumno
    ]);
}

// Dar de alta
public function altaAlumno($id)
{
    $alumno = Alumno::find($id);

    if (!$alumno) {
        return response()->json(['error' => 'Alumno no encontrado'], 404);
    }

    $alumno->update(['status' => 'activo']);

    return response()->json([
        'success' => true,
        'message' => 'Alumno dado de alta correctamente',
        'alumno' => $alumno
    ]);
}


    /**
     * Endpoint para la tabla de tu frontend
     */
   public function datosCombinados(Request $request)
{
    // 1. Recibe el parámetro status
    $statusParam = strtolower($request->query('status', 'activo'));

    // 2. Convertimos numérico a texto
    if ($statusParam === '1') $statusParam = 'activo';
    if ($statusParam === '0') $statusParam = 'inactivo';

    // 3. Validamos que solo acepte estos dos valores
    if (!in_array($statusParam, ['activo', 'inactivo'])) {
        return response()->json(['error' => 'Estado inválido'], 400);
    }

    // 4. Consulta con relación clases
    $alumnos = \App\Models\Alumno::with('clases')
        ->where('status', $statusParam)
        ->get()
        ->map(function ($alumno) {
            return [
                'id' => $alumno->id,
                'nombre' => $alumno->nombre . ' ' . $alumno->apellido,
                'telefono' => $alumno->telefono ?? '-',
                'clases' => $alumno->clases->pluck('nombre_clase')->join(', ') ?: '-',
            ];
        });

    return response()->json($alumnos);
}


}
