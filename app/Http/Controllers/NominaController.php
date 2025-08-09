<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\RegistroNomina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class NominaController extends Controller
{
    // =========================
    // CRUD BÁSICO (api/nominas)
    // =========================

    // GET /api/nominas
    public function index()
    {
        $nominas = Nomina::all();
        $totalfinal = 0;
        $num = 1;

        if ($nominas->count() > 0) {
            $response = [];
            foreach ($nominas as $result) {
                $fecha = $result->fecha;
                $id_autor = $result->id_autor;

                $ano = date("Y", strtotime($fecha));
                $mes = date("M", strtotime($fecha));
                $dia = date("d", strtotime($fecha));

                $meses = [
                    'Jan' => 'ENERO', 'Feb' => 'FEBRERO', 'Mar' => 'MARZO',
                    'Apr' => 'ABRIL', 'May' => 'MAYO', 'Jun' => 'JUNIO',
                    'Jul' => 'JULIO', 'Aug' => 'AGOSTO', 'Sep' => 'SEPTIEMBRE',
                    'Oct' => 'OCTUBRE', 'Nov' => 'NOVIEMBRE', 'Dec' => 'DICIEMBRE'
                ];

                $mes = $meses[$mes] ?? $mes;
                $fechaForm = strtoupper("$dia DE $mes DEL $ano");

                $autor = DB::table('usuarios')->where('id', $id_autor)->value('nombre');

                $response[] = [
                    ...$result->toArray(),
                    'num' => $num,
                    'formated_fecha' => $fechaForm,
                    'autor' => $autor,
                ];

                $num++;
                $totalfinal += (float) $result->total;
            }

            return response()->json([
                'data' => $response,
                'totalfinal' => number_format($totalfinal, 2)
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'NO HAY NÓMINAS REALIZADAS'
        ], 200);
    }

    // GET /api/nominas/{id}
    public function show($id)
    {
        $nomina = Nomina::find($id);
        if (!$nomina) {
            return response()->json(['message' => 'Nómina no encontrada'], 404);
        }
        return response()->json($nomina, 200);
    }

    // POST /api/nominas
    public function store(Request $request)
    {
        // Valida según tus reglas (aquí va simple)
        $data = $request->validate([
            'fecha' => 'required|date',
            'id_autor' => 'required|integer',
            'clases' => 'required|numeric',
            'inscripciones' => 'required|numeric',
            'recargos' => 'required|numeric',
            'total' => 'required|numeric',
            'comisiones' => 'required|numeric',
            'total_neto' => 'required|numeric',
            'porcentaje_comision' => 'required|numeric',
        ]);

        $nomina = Nomina::create($data);
        return response()->json($nomina, 201);
    }

    // PUT/PATCH /api/nominas/{id}
    public function update(Request $request, $id)
    {
        $nomina = Nomina::find($id);
        if (!$nomina) {
            return response()->json(['message' => 'Nómina no encontrada'], 404);
        }

        $nomina->update($request->all());
        return response()->json($nomina, 200);
    }

    // DELETE /api/nominas/{id}
    public function destroy($id)
    {
        $nomina = Nomina::find($id);
        if (!$nomina) {
            return response()->json(['message' => 'Nómina no encontrada'], 404);
        }
        $nomina->delete();
        return response()->json(['message' => 'Nómina eliminada'], 200);
    }

    // =========================
    // FILTROS / UTILIDADES
    // =========================

    // GET /api/nominas/mostrar/{anio}
    public function mostrarNominas($anio)
    {
        $nominas = DB::table('nominas')
            ->whereYear('fecha', $anio)
            ->orderBy('fecha', 'DESC')
            ->get();

        if ($nominas->count() > 0) {
            $response = [];
            $num = 1;
            $totalfinal = 0;

            foreach ($nominas as $result) {
                $fecha = $result->fecha;
                $id_autor = $result->id_autor;

                $ano = date("Y", strtotime($fecha));
                $mes = date("M", strtotime($fecha));
                $dia = date("d", strtotime($fecha));

                $meses = [
                    'Jan' => 'ENERO', 'Feb' => 'FEBRERO', 'Mar' => 'MARZO',
                    'Apr' => 'ABRIL', 'May' => 'MAYO', 'Jun' => 'JUNIO',
                    'Jul' => 'JULIO', 'Aug' => 'AGOSTO', 'Sep' => 'SEPTIEMBRE',
                    'Oct' => 'OCTUBRE', 'Nov' => 'NOVIEMBRE', 'Dec' => 'DICIEMBRE'
                ];

                $mes = $meses[$mes] ?? $mes;
                $fechaForm = strtoupper("$dia DE $mes DEL $ano");

                $autor = DB::table('usuarios')->where('id', $id_autor)->value('nombre');

                $response[] = [
                    ...(array) $result,
                    'num' => $num,
                    'formated_fecha' => $fechaForm,
                    'autor' => $autor,
                ];

                $num++;
                $totalfinal += (float) $result->total;
            }

            return response()->json([
                'data' => $response,
                'totalfinal' => number_format($totalfinal, 2)
            ], 200);
        }

        return response()->json([
            'data' => [],
            'message' => 'NO HAY NÓMINAS REALIZADAS'
        ], 200);
    }

    // GET /api/nominas/anios
    public function mostrarAnios()
    {
        $anios = DB::table('nominas')
            ->selectRaw('DISTINCT YEAR(fecha) as anio')
            ->orderBy('anio', 'DESC')
            ->get();

        return response()->json($anios, 200);
    }

    // =========================
    // GENERAR NÓMINA (corte)
    // =========================

    // POST /api/nominas/generar   (body: { id: <id_autor> })
    public function generarNomina(Request $request)
    {
        $fecha = date("Y-m-d");
        $gran_total_clases = 0;
        $gran_total_comisiones = 0;
        $gran_total_inscripciones = 0;
        $gran_total_recargos = 0;
        $porcentaje_comision = 0.1;

        $filas = DB::table('pagos_fragmentados')->where('nomina', 0)->count();
        $filas_2 = DB::table('pagos_secundarios')->where('nomina', 0)->count();

        if ($filas == 0 && $filas_2 == 0) {
            return response()->json([
                'message' => 'No se puede realizar el corte porque no hay ingresos aplicables registrados.'
            ], 400);
        }

        $nomina = Nomina::create([
            'fecha' => "2021-01-01", // valor temporal antes de calcular
            'id_autor' => "",
            'clases' => 0,
            'inscripciones' => 0,
            'recargos' => 0,
            'total' => 0,
            'comisiones' => 0,
            'total_neto' => 0,
            'porcentaje_comision' => 0
        ]);

        $id_nomina = $nomina->id_nomina;

        // Sumar por clases
        if ($filas != 0) {
            $programas = DB::table('programas_predefinidos')->orderBy('id_programa')->get();

            foreach ($programas as $programa) {
                $clases = DB::table('clases')->where('id_programa', $programa->id_programa)->get();

                foreach ($clases as $clase) {
                    $id_maestro = $clase->id_maestro;
                    $id_clase   = $clase->id_clase;
                    $total_clase = 0;

                    $pagos_fragmentados = DB::table('pagos_fragmentados')
                        ->where('id_clase', $id_clase)
                        ->where('nomina', 0)
                        ->get();

                    foreach ($pagos_fragmentados as $pago) {
                        $total_clase += (float) $pago->monto;
                    }

                    $total_comision = $total_clase * $porcentaje_comision;
                    $total_neto = $total_clase - $total_comision;

                    $gran_total_clases += $total_clase;
                    $gran_total_comisiones += $total_comision;

                    DB::table('registro_nominas')->insert([
                        'id_nomina' => $id_nomina,
                        'id_maestro' => $id_maestro,
                        'id_clase' => $id_clase,
                        'total' => $total_clase,
                        'comision' => $total_comision,
                        'total_neto' => $total_neto
                    ]);
                }
            }
        }

        // Inscripciones y recargos
        if ($filas_2 != 0) {
            $pagos_inscripciones = DB::table('pagos_secundarios')
                ->where('concepto', 'INSCRIPCION')
                ->where('nomina', 0)
                ->get();

            foreach ($pagos_inscripciones as $pago) {
                $gran_total_inscripciones += (float) $pago->monto;
            }

            $pagos_recargos = DB::table('pagos_secundarios')
                ->where('concepto', 'RECARGO')
                ->where('nomina', 0)
                ->get();

            foreach ($pagos_recargos as $pago) {
                $gran_total_recargos += (float) $pago->monto;
            }
        }

        $ingresos_totales = $gran_total_clases + $gran_total_inscripciones + $gran_total_recargos;
        $gran_total_neto  = $ingresos_totales - $gran_total_comisiones;

        // Finaliza nómina
        $nomina->fecha = $fecha;
        $nomina->id_autor = $request->id;
        $nomina->clases = $gran_total_clases;
        $nomina->inscripciones = $gran_total_inscripciones;
        $nomina->recargos = $gran_total_recargos;
        $nomina->total = $ingresos_totales;
        $nomina->comisiones = $gran_total_comisiones;
        $nomina->total_neto = $gran_total_neto;
        $nomina->porcentaje_comision = $porcentaje_comision;
        $nomina->save();

        // Marcar pagos como procesados
        DB::table('pagos_fragmentados')->where('nomina', 0)->update(['nomina' => $id_nomina]);
        DB::table('pagos_secundarios')->where('nomina', 0)->update(['nomina' => $id_nomina]);

        return response()->json([
            'message' => 'Nómina generada exitosamente.',
            'id_nomina' => $id_nomina,
            'fecha' => $fecha,
            'ingresos_totales' => $ingresos_totales,
            'gran_total_neto' => $gran_total_neto,
            'gran_total_comisiones' => $gran_total_comisiones
        ], 200);
    }

    // =====================================
    // INFORME (JSON / VISTA / PDF)
    // =====================================

    // endpoint JSON que ya usa tu frontend
    public function informeNomina($id_nomina)
    {
        $info = $this->buildInformeData($id_nomina);
        return response()->json($info, 200);
    }

    // VISTA imprimible (web.php)
    public function informeNominaPrint($id)
    {
        $info = $this->buildInformeData($id);
        return view('nominas.informe', $info);
    }

    // PDF descargable (web.php)
    public function informeNominaPdf($id)
    {
        $info = $this->buildInformeData($id);
        $pdf  = Pdf::loadView('nominas.informe', $info)->setPaper('letter', 'portrait');
        return $pdf->download('informe_nomina_'.$id.'.pdf');
    }

    // -------------------------
    // Helper: arma la data única
    // -------------------------
    private function buildInformeData($id_nomina)
    {
        $nominaInfo = DB::table('nominas')
            ->where('id_nomina', $id_nomina)
            ->select('fecha', 'id_autor','clases','inscripciones','recargos','total','comisiones','total_neto')
            ->first();

        if (!$nominaInfo) {
            abort(404, 'Nómina no encontrada');
        }

        $autor = DB::table('usuarios')->where('id', $nominaInfo->id_autor)->value('nombre');

        $ano = date("Y", strtotime($nominaInfo->fecha));
        $mes = date("M", strtotime($nominaInfo->fecha));
        $dia = date("d", strtotime($nominaInfo->fecha));
        $meses = [
            'Jan'=>'ENERO','Feb'=>'FEBRERO','Mar'=>'MARZO','Apr'=>'ABRIL','May'=>'MAYO','Jun'=>'JUNIO',
            'Jul'=>'JULIO','Aug'=>'AGOSTO','Sep'=>'SEPTIEMBRE','Oct'=>'OCTUBRE','Nov'=>'NOVIEMBRE','Dec'=>'DICIEMBRE'
        ];
        $fechanomina = strtoupper($dia.' DE '.($meses[$mes] ?? $mes).' DEL '.$ano);

        $maestros = DB::table('registro_nominas')
            ->where('id_nomina', $id_nomina)
            ->select('id_maestro')
            ->distinct()
            ->orderBy('id_maestro')
            ->get();

        $data = [];
        foreach ($maestros as $m) {
            $id_maestro = $m->id_maestro;
            $nombre_maestro = DB::table('maestros')->where('id_maestro', $id_maestro)->value('nombre');

            $registros = DB::table('registro_nominas')
                ->where('id_maestro', $id_maestro)
                ->where('id_nomina', $id_nomina)
                ->get();

            $clases = [];
            $totalgenerado = 0;
            $totalmaestro  = 0;

            foreach ($registros as $r) {
                $nombre_clase = DB::table('clases')->where('id_clase', $r->id_clase)->value('nombre');
                $clases[] = [
                    'nombre_clase' => $nombre_clase,
                    'total'        => number_format($r->total, 2),
                    'comision'     => number_format($r->comision, 2),
                ];
                $totalgenerado += (float) $r->total;
                $totalmaestro  += (float) $r->comision;
            }

            $data[] = [
                'nombre_maestro' => $nombre_maestro,
                'clases'         => $clases,
                'totalgenerado'  => number_format($totalgenerado, 2),
                'totalmaestro'   => number_format($totalmaestro, 2),
            ];
        }

        $totals = [
            'mensualidades' => number_format($nominaInfo->clases, 2),
            'inscripciones' => number_format($nominaInfo->inscripciones, 2),
            'recargos'      => number_format($nominaInfo->recargos, 2),
            'total'         => number_format($nominaInfo->total, 2),
            'comisiones'    => number_format($nominaInfo->comisiones, 2),
            'total_neto'    => number_format($nominaInfo->total_neto, 2),
        ];

        return [
            'fechanomina' => $fechanomina,
            'autor'       => $autor,
            'folio'       => $id_nomina,
            'data'        => $data,
            'totals'      => $totals,
        ];
    }
}
