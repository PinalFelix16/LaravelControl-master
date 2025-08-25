<?php

namespace App\Http\Controllers;

use App\Models\Programa;
use App\Models\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramaController extends Controller
{
    public function index() { return Programa::all(); }

    public function show($id) { return Programa::findOrFail($id); }

    public function store(Request $request) {
        $programa = Programa::create($request->all());
        return response()->json($programa, 201);
    }

    public function update(Request $request, $id) {
        $programa = Programa::findOrFail($id);
        $programa->update($request->all());
        return response()->json($programa, 200);
    }

    public function destroy($id) {
        Programa::destroy($id);
        return response()->json(null, 204);
    }

    // ---------- NUEVO: crear programa + clases ----------
    public function storeWithClases(Request $request)
    {
        $validated = $request->validate([
            'programa' => ['required','array'],
            'programa.nombre'       => ['required','string','max:255'],
            'programa.mensualidad'  => ['nullable','numeric'],
            'programa.nivel'        => ['nullable','string','max:255'],
            'programa.complex'      => ['nullable','in:0,1'],

            'clases'   => ['required','array','min:1'],
            'clases.*.nombre'       => ['required','string','max:30'],
            'clases.*.id_maestro'   => ['required','string','max:6'],
            'clases.*.informacion'  => ['nullable','string','max:100'],
            'clases.*.porcentaje'   => ['nullable','numeric'],
            'clases.*.personal'     => ['nullable','integer'],
            // opcionales (si luego los agregas en el UI)
            'clases.*.lugar'        => ['nullable','string','max:100'],
            'clases.*.hora_inicio'  => ['nullable','date_format:H:i'],
            'clases.*.hora_fin'     => ['nullable','date_format:H:i'],
            'clases.*.dias'         => ['nullable'], // array|string CSV
        ]);

        $out = DB::transaction(function () use ($validated) {
            // 1) Crear programa
            $p = Programa::create([
                'nombre'      => $validated['programa']['nombre'],
                'mensualidad' => $validated['programa']['mensualidad'] ?? 0,
                'nivel'       => $validated['programa']['nivel'] ?? null,
                'complex'     => isset($validated['programa']['complex']) ? (int)$validated['programa']['complex'] : 0,
                'status'      => 1,
                'ocultar'     => 0,
            ]);

            // 2) Crear clases
            $created = [];
            foreach ($validated['clases'] as $c) {
                $clase = Clase::create([
                    'id_programa' => $p->id_programa,
                    'alumno_id'   => 0,
                    'nombre'      => $c['nombre'],
                    'id_maestro'  => $c['id_maestro'],
                    'informacion' => $c['informacion'] ?? null,
                    'lugar'       => $c['lugar'] ?? null,
                    'hora_inicio' => $c['hora_inicio'] ?? null,
                    'hora_fin'    => $c['hora_fin'] ?? null,
                    'dias'        => $c['dias'] ?? null, // si mandas array, el mutator lo convierte a CSV
                    'mensualidad' => $validated['programa']['mensualidad'] ?? null,
                    'complejo'    => isset($validated['programa']['complex']) ? (int)$validated['programa']['complex'] : 0,
                    'porcentaje'  => $c['porcentaje'] ?? null,
                    'personal'    => $c['personal'] ?? 0,
                ]);
                $created[] = $clase;
            }

            // ---------- SOLUCIÓN DSG: sanear respuesta para evitar accessors/appends ----------
            // No devolver los modelos "en crudo" porque al serializar intentarían
            // evaluar atributos calculados que dependen de relaciones (p.ej. programa_nombre)
            // y eso dispara la clase inexistente ProgramaPredefinido.
            // Convertimos a arrays planos, sin appends ni relaciones.

            // Programa plano
            if (method_exists($p, 'setAppends')) { $p->setAppends([]); }
            $progArray = $p->attributesToArray();

            // Clases planas
            $clasesArray = [];
            foreach ($created as $cl) {
                if (method_exists($cl, 'setAppends')) { $cl->setAppends([]); }
                // por si la relación se hubiera inicializado en algún accessor:
                if (method_exists($cl, 'unsetRelation')) { $cl->unsetRelation('programa'); }
                $clasesArray[] = $cl->attributesToArray();
            }
            // ---------- /SOLUCIÓN DSG ----------

            return ['programa' => $progArray, 'clases' => $clasesArray];
        });

        return response()->json($out, 201);
    }
}
