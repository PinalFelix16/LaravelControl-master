<?php

namespace App\Http\Controllers;

use App\Models\Maestro;
use Illuminate\Http\Request;

class MaestroController extends Controller
{
    public $timestamps = false;

    /**
     * Devuelve la lista de maestros con el "shape" que tu frontend espera:
     * - id_maestro
     * - nombre_maestro (alias de 'nombre')
     * - clases (array) para evitar errores de render en el map()
     * Soporta filtro opcional ?status=0|1
     */
    public function index(Request $request)
    {
        $status = $request->query('status', null); // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        $query  = Maestro::query();                // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        if ($status !== null && $status !== '') {  // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            $query->where('status', (int)$status); // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        }                                          // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA

        $maestros = $query->get()->map(function ($m) { // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            return [                                    // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'id_maestro'     => $m->id_maestro,     // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'nombre_maestro' => $m->nombre,         // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'nombre_titular' => $m->nombre_titular, // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'direccion'      => $m->direccion,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'fecha_nac'      => $m->fecha_nac,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'rfc'            => $m->rfc,            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'celular'        => $m->celular,        // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'status'         => (int)$m->status,    // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
                'clases'         => [],                 // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA (placeholder para evitar crash del map)
            ];                                          // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        });                                             // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA

        return response()->json($maestros);            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
    }

    // Crear un nuevo maestro
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_titular' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'fecha_nac' => 'nullable|date',
            'rfc' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'status' => 'required|integer|in:0,1'
        ]);

        $maestro = Maestro::create($request->all());

        // Normalizamos el shape de respuesta para el frontend
        $payload = [                                      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'id_maestro'     => $maestro->id_maestro,     // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre_maestro' => $maestro->nombre,         // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre_titular' => $maestro->nombre_titular, // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'direccion'      => $maestro->direccion,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'fecha_nac'      => $maestro->fecha_nac,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'rfc'            => $maestro->rfc,            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'celular'        => $maestro->celular,        // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'status'         => (int)$maestro->status,    // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'clases'         => [],                       // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        ];                                                // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA

        return response()->json($payload, 201);          // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
    }

    // Mostrar un maestro específico (shape esperado por tu EditForm y tabla)
    public function show($id)
    {
        $m = Maestro::findOrFail($id);                    // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        $payload = [                                      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'id_maestro'     => $m->id_maestro,           // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre_maestro' => $m->nombre,               // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre'         => $m->nombre,               // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA (para formularios)
            'nombre_titular' => $m->nombre_titular,       // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'direccion'      => $m->direccion,            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'fecha_nac'      => $m->fecha_nac,            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'rfc'            => $m->rfc,                  // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'celular'        => $m->celular,              // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'status'         => (int)$m->status,          // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'clases'         => [],                       // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        ];                                                // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA

        return response()->json($payload);               // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
    }

    // Actualizar un maestro
    public function update(Request $request, $id)
    {
        $maestro = Maestro::findOrFail($id);

        $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'nombre_titular' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'fecha_nac' => 'nullable|date',
            'rfc' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'status' => 'sometimes|required|integer|in:0,1'
        ]);

        $maestro->update($request->all());

        // Responder con el mismo shape esperado por el frontend
        $payload = [                                      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'id_maestro'     => $maestro->id_maestro,     // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre_maestro' => $maestro->nombre,         // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre'         => $maestro->nombre,         // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'nombre_titular' => $maestro->nombre_titular, // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'direccion'      => $maestro->direccion,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'fecha_nac'      => $maestro->fecha_nac,      // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'rfc'            => $maestro->rfc,            // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'celular'        => $maestro->celular,        // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'status'         => (int)$maestro->status,    // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
            'clases'         => [],                       // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
        ];                                                // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA

        return response()->json($payload);               // <-- PROTOCOLO ROJO: LÍNEA AÑADIDA
    }

    // Eliminar un maestro
    public function destroy($id)
    {
        $maestro = Maestro::find($id);
        if (!$maestro) {
            return response()->json(['message' => 'Maestro no encontrado.'], 404);
        }
        $maestro->delete();
        return response()->json(null, 204);
    }
}
