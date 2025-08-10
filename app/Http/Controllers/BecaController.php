<?php

namespace App\Http\Controllers;

use App\Models\AdeudoPrograma;
use App\Models\AdeudoFragmentado;
use App\Models\RegistroBeca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class BecaController extends Controller
{
    public function index(Request $r)
    {
        return RegistroBeca::query()
            ->when($r->id_alumno,   fn($q) => $q->where('id_alumno',   $r->id_alumno))
            ->when($r->id_programa, fn($q) => $q->where('id_programa', $r->id_programa))
            ->when($r->periodo,     fn($q) => $q->where('periodo',     $r->periodo))
            ->orderByDesc('fecha')
            ->paginate(20);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'id_alumno'     => 'required|integer',
            'id_programa'   => 'required|integer',
            'periodo'       => 'required|string|max:32',
            'porcentaje'    => 'required|numeric|min:0|max:100',
            'precio_origen' => 'nullable|numeric|min:0',
            'tipo'          => 'nullable|string|max:30',
            'observaciones' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data) {
            // Solo columnas que existen en adeudos_programas
            $match = Arr::only($data, ['id_alumno','id_programa','periodo']);
            // (si prefieres evitar Arr: $match = ['id_alumno'=>$data['id_alumno'],'id_programa'=>$data['id_programa'],'periodo'=>$data['periodo']];)

            $adeudo = AdeudoPrograma::where($match)->first();
            if (!$adeudo) {
                abort(404, 'Adeudo no encontrado para ese periodo');
            }

            $precio_anterior = $data['precio_origen'] ?? (float) $adeudo->monto;
            $precio_final    = round($precio_anterior * (1 - ($data['porcentaje'] / 100)), 2);

            // Actualiza adeudo: beca fija y anula descuento (como en tu sistema anterior)
            $adeudo->update([
                'monto'     => $precio_final,
                'beca'      => $data['porcentaje'],
                'descuento' => 0,
            ]);

            // Propaga a fragmentados
            AdeudoFragmentado::where($match)->get()->each(function ($f) use ($precio_final) {
                $f->update([
                    'monto' => round($precio_final * ((float) $f->porcentaje / 100), 2),
                ]);
            });

            // Registro histórico
            $log = RegistroBeca::create([
                'id_alumno'       => $data['id_alumno'],
                'id_programa'     => $data['id_programa'],
                'periodo'         => $data['periodo'],
                'precio_anterior' => $precio_anterior,
                'precio_final'    => $precio_final,
                'porcentaje'      => $data['porcentaje'],
                'tipo'            => $data['tipo'] ?? 'BECA',
                'observaciones'   => $data['observaciones'] ?? null,
                'fecha'           => now(),
            ]);

            return response()->json(['ok' => true, 'adeudo' => $adeudo, 'beca' => $log], 201);
        });
    }

    public function show($id) { return RegistroBeca::findOrFail($id); }
    public function update(Request $r, $id) { abort(405); }
    public function destroy($id) { abort(405); }
}
