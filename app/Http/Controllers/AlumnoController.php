<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    /**
     * GET /api/alumnos?status=0|1
     */
    public function index(Request $request)
    {
        $q = Alumno::query();

        if ($request->has('status')) {
            $s = (int) $request->query('status'); // 0/1
            $q->where('status', $s);
        }

        return response()->json($q->orderBy('id_alumno', 'asc')->get());
    }

    /**
     * GET /api/alumnos/datos-combinados?status=0|1
     * Requiere relación ->clases() en el modelo
     */
    public function datosCombinados(Request $request)
    {
        $q = Alumno::query()
            ->select('alumnos.*')
            ->withCount('clases');

        if ($request->has('status')) {
            $q->where('status', (int) $request->query('status'));
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
     * Normaliza entrada del front:
     * - Acepta fecha_nacimiento y la mapea a fecha_nac
     * - Castea status a int 0/1
     * - Castea beca a float
     */
    private function normalize(array $data): array
    {
        if (!isset($data['fecha_nac']) && isset($data['fecha_nacimiento'])) {
            $data['fecha_nac'] = $data['fecha_nacimiento'];
            unset($data['fecha_nacimiento']);
        }

        if (isset($data['status'])) {
            $data['status'] = (int) $data['status'] === 1 ? 1 : 0;
        }

        if (isset($data['beca']) && $data['beca'] !== '' && $data['beca'] !== null) {
            $data['beca'] = (float) $data['beca'];
        }

        return $data;
    }

    /**
     * POST /api/alumnos
     */
    public function store(Request $request)
    {
        $input = $this->normalize($request->all());

        $v = Validator::make($input, [
            'nombre'        => 'required|string|min:2',
            'apellido'      => 'nullable|string',           // quita si tu tabla no lo tiene
            'correo'        => 'nullable|email|unique:alumnos,correo',
            'celular'       => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:50',
            'fecha_nac'     => 'nullable|date',
            'tutor'         => 'nullable|string|max:255',
            'tutor_2'       => 'nullable|string|max:255',
            'telefono_2'    => 'nullable|string|max:50',
            'hist_medico'   => 'nullable|string',
            'beca'          => 'nullable|numeric',
            'status'        => 'nullable|in:0,1',
            'observaciones' => 'nullable|string',
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

        $input = $this->normalize($request->all());

        $v = Validator::make($input, [
            'nombre'        => 'sometimes|required|string|min:2',
            'apellido'      => 'nullable|string', // quita si tu tabla no lo tiene
            'correo'        => 'nullable|email|unique:alumnos,correo,' . $id . ',id_alumno',
            'celular'       => 'nullable|string|max:50',
            'telefono'      => 'nullable|string|max:50',
            'fecha_nacimiento'     => 'nullable|date',
            'tutor'         => 'nullable|string|max:255',
            'tutor_2'       => 'nullable|string|max:255',
            'telefono_2'    => 'nullable|string|max:50',
            'hist_medico'   => 'nullable|string',
            'beca'          => 'nullable|numeric',
            'status'        => 'nullable|in:0,1',
            'observaciones' => 'nullable|string',
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
     * ALTA / BAJA
     * PUT|PATCH /api/alumnos/{id}/alta
     * PUT|PATCH /api/alumnos/{id}/baja
     */
    public function altaAlumno($id) { return $this->alta($id); }
    public function bajaAlumno($id) { return $this->baja($id); }

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
     * Expediente (si lo usas)
     */
    public function expediente($id)
    {
        $alumno = Alumno::where('id_alumno', $id)->first();
        if (!$alumno) return response()->json(['error' => 'Alumno no encontrado'], 404);

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
