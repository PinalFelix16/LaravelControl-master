<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class RecargoController extends Controller
{
    public function agregarRecargos(): JsonResponse
    {
        $informacion = DB::table('informacion')->first();

        if (!$informacion) {
            return response()->json(['message' => 'No hay información de configuración'], 400);
        }

        $titulo = $informacion->nombre_corto;
        $nombre_completo = $informacion->nombre;
        $version = $informacion->version;

        // Definir periodo
        $mes = date("m");
        $anio = date("Y");

        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO',
            '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO',
            '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE',
            '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];

        $periodo = $meses[$mes] . '/' . $anio;

        // Verificar si ya existen recargos para el periodo actual
        $existe = DB::table('periodos_recargos')
                    ->where('mes', $mes)
                    ->where('anio', $anio)
                    ->exists();

        if ($existe) {
            return response()->json(['message' => 'Ya existen recargos para el periodo actual'], 400);
        }

        $monto = $informacion->precio_recargo;
        $alumnos = DB::table('alumnos')->get();

        foreach ($alumnos as $alumno) {
            $id = $alumno->id;
            $adeudo = DB::table('adeudos_programas')
                        ->where('id_alumno', $id)
                        ->count();

            if ($adeudo != 0) {
                DB::table('adeudos_secundarios')->insert([
                    'id_alumno' => $id,
                    'concepto' => 'RECARGO',
                    'periodo' => $periodo,
                    'monto' => $monto,
                    'descuento' => 0,
                    'corte' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        DB::table('periodos_recargos')->insert([
            'mes' => $mes,
            'anio' => $anio,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Recargos agregados con éxito'], 200);
    }
}
