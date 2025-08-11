<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Corte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class CorteController extends Controller
{
    /* ==========================
     * Helpers privados
     * ========================== */

    /** where: registros pendientes de corte (soporta NULL, 0, "0" y "") */
    private function scopePendiente($builder)
{
    // Solo NULL o 0. Nada de '' (cadena vacía)
    return $builder->where(function ($q) {
        $q->whereNull('corte')
          ->orWhere('corte', 0);
    });
}

    /** nombre de mes en ES en mayúsculas */
    private function monthName($m)
    {
        $meses = [1=>'ENERO',2=>'FEBRERO',3=>'MARZO',4=>'ABRIL',5=>'MAYO',6=>'JUNIO',7=>'JULIO',8=>'AGOSTO',9=>'SEPTIEMBRE',10=>'OCTUBRE',11=>'NOVIEMBRE',12=>'DICIEMBRE'];
        return $meses[(int)$m] ?? (string)$m;
    }

    /** fecha tipo "LUNES, 05 DE AGOSTO DEL 2025" */
    private function fechaLargaES($fechaYmd)
    {
        $dias = ["DOMINGO","LUNES","MARTES","MIÉRCOLES","JUEVES","VIERNES","SÁBADO"];
        $t    = strtotime($fechaYmd);
        $diaN = (int)date('w',$t);
        $dia  = strtoupper(date('d',$t));
        $mesN = (int)date('m',$t);
        $ano  = date('Y',$t);
        return $dias[$diaN] . ", " . $dia . " DE " . $this->monthName($mesN) . " DEL " . $ano;
    }

    /** normaliza item para unión (programa/secundario) */
    private function normalizaItem($tipo, $row, $extra = [])
    {
        return array_merge([
            'tipo'            => $tipo,
            'num'             => $extra['num'] ?? null,
            'recibo'          => $row->recibo ?? '-',
            'id_alumno'       => $row->id_alumno ?? null,
            'alumno'          => $extra['alumno'] ?? ($row->alumno ?? '-'),
            'concepto'        => $extra['concepto'] ?? ($row->concepto ?? '-'),
            'nombre_programa' => $row->nombre_programa ?? null,
            'periodo'         => $row->periodo ?? '-',
            'fecha'           => $extra['fecha'] ?? ($row->fecha ?? $row->fecha_pago ?? '-'),
            'monto'           => number_format((float)($row->monto ?? 0), 2),
            'descuento'       => number_format((float)($row->descuento ?? 0), 2),
            'beca'            => number_format((float)($row->beca ?? 0), 2),
        ], $extra);
    }

    /* ==========================
     * Endpoints
     * ========================== */

    // Trae todos los cortes
    public function index()
    {
        $cortes = Corte::orderBy('fecha','desc')->get();
        return response()->json($cortes);
    }

    // Pendientes de corte (caja)
    public function corteCaja(Request $request)
    {
        $total = 0;
        $num   = 1;

        $concepto = $request->input('concepto');
        $monto    = $request->input('monto');

        // Pagos programas (pendientes)
        $pagosProgramasQuery = $this->scopePendiente(DB::table('pagos_programas'));
        if ($concepto) $pagosProgramasQuery->where('concepto','like',"%$concepto%");
        if ($monto)    $pagosProgramasQuery->where('monto',$monto);

        $pagosProgramas = $pagosProgramasQuery
            ->orderBy('id_programa','desc')
            ->orderBy('periodo','desc')
            ->get();

        $programaData = [];
        foreach ($pagosProgramas as $pago) {
            $programaNombre = 'VISITA';
            if ($pago->id_programa !== '000') {
                $programa = DB::table('programas_predefinidos')->where('id_programa',$pago->id_programa)->first();
                $programaNombre = $programa->nombre ?? 'Desconocido';
            }
            $alumno        = DB::table('alumnos')->where('id_alumno',$pago->id_alumno)->first();
            $alumnoNombre  = $alumno->nombre ?? 'Alumno no encontrado';
            $fecha         = strtoupper(date("d/M/Y", strtotime($pago->fecha_pago)));

            $programaData[] = $this->normalizaItem('programa', $pago, [
                'num'     => $num++,
                'alumno'  => $alumnoNombre,
                'concepto'=> $programaNombre,
                'fecha'   => $fecha,
            ]);
            $total += (float)$pago->monto;
        }

        // Pagos secundarios (pendientes)
        $pagosSecundarios = $this->scopePendiente(DB::table('pagos_secundarios'))->get();
        $secundarioData   = [];
        foreach ($pagosSecundarios as $pago) {
            $alumno        = DB::table('alumnos')->where('id_alumno',$pago->id_alumno)->first();
            $alumnoNombre  = $alumno->nombre ?? 'Alumno no encontrado';
            $fecha         = strtoupper(date("d/M/Y", strtotime($pago->fecha_pago)));

            $secundarioData[] = $this->normalizaItem('secundario', $pago, [
                'num'     => $num++,
                'alumno'  => $alumnoNombre,
                'fecha'   => $fecha,
            ]);
            $total += (float)$pago->monto;
        }

        // Miscelánea (pendientes)
        $miscelanea = DB::table('miscelanea')->where('corte', 0)->get();
        $miscelaneosData = [];
        foreach ($miscelanea as $pago) {
            $fecha = $pago->created_at ? strtoupper(date("d/M/Y", strtotime($pago->created_at))) : "-";
            $miscelaneosData[] = [
                'num'      => $num++,
                'recibo'   => '-',
                'alumno'   => '-',
                'concepto' => $pago->descripcion ?? 'MISCELÁNEA',
                'periodo'  => '-',
                'fecha'    => $fecha,
                'monto'    => number_format((float)($pago->monto ?? 0), 2),
            ];
            $total += (float)($pago->monto ?? 0);
        }

        return response()->json([
            'programaData'   => $programaData,
            'secundarioData' => $secundarioData,
            'miscelanioData' => $miscelaneosData,
            'total'          => number_format($total, 2),
        ]);
    }

    public function getCortesPorAnio($anio)
    {
        $totalfinal = 0;
        $num = 1;

        $cortes = DB::table('cortes')
            ->select(DB::raw('DISTINCT MONTH(fecha) as mesnum'))
            ->whereYear('fecha', $anio)
            ->orderBy('fecha','desc')
            ->get();

        $data = [];
        foreach ($cortes as $corte) {
            $mesnum = $corte->mesnum;
            $total  = DB::table('cortes')
                ->whereYear('fecha',$anio)
                ->whereMonth('fecha',$mesnum)
                ->sum('total');

            $data[] = [
                'num'    => $num++,
                'mes'    => $this->monthName($mesnum) . " DEL " . $anio,
                'total'  => number_format($total,2),
                'mesnum' => $mesnum
            ];
            $totalfinal += (float)$total;
        }

        $anios = DB::table('cortes')
            ->select(DB::raw('DISTINCT YEAR(fecha) as anio'))
            ->orderBy('fecha','desc')->get();

        return response()->json([
            'data'       => $data,
            'totalfinal' => number_format($totalfinal,2),
            'anios'      => $anios,
            'anio'       => (int)$anio
        ]);
    }

    public function getCortesPorMes($anio, $mes)
    {
        $mesnombre   = $this->monthName($mes);
        $totalfinal  = 0;
        $num         = 1;

        $cortes = DB::table('cortes')
            ->whereMonth('fecha',$mes)
            ->whereYear('fecha',$anio)
            ->orderBy('fecha','desc')
            ->get();

        if ($cortes->isEmpty()) {
            return response()->json(['message' => 'NO HAY CORTES DE CAJA REGISTRADOS ESTE MES'], 404);
        }

        $data = [];
        foreach ($cortes as $corte) {
            $autor = $corte->id_autor;
            if (is_numeric($autor)) {
                $u = DB::table('usuarios')->where('id',(int)$autor)->first();
                $autor = $u ? ($u->nombre ?: $u->usuario ?: $autor) : $autor;
            }

            $data[] = [
                'num'      => $num++,
                'fecha'    => $this->fechaLargaES($corte->fecha),
                'autor'    => $autor,
                'total'    => number_format((float)$corte->total,2),
                'id_corte' => $corte->id_corte
            ];
            $totalfinal += (float)$corte->total;
        }

        return response()->json([
            'data'       => $data,
            'totalfinal' => number_format($totalfinal,2),
            'mes'        => $mesnombre,
            'anio'       => (int)$anio
        ]);
    }

    public function getPagosPorCorte($id_corte)
    {
        $total = 0;
        $num   = 1;

        // Importante: permitir match con varchar con ceros
        $pagosProgramas = DB::table('pagos_programas')
            ->leftJoin('programas_predefinidos','pagos_programas.id_programa','=','programas_predefinidos.id_programa')
            ->select('pagos_programas.*','programas_predefinidos.nombre as nombre_programa')
            ->where(function($q) use ($id_corte) {
                $q->where('pagos_programas.corte', $id_corte)
                  ->orWhereRaw('CAST(pagos_programas.corte AS UNSIGNED) = ?', [$id_corte]);
            })
            ->orderBy('pagos_programas.id_alumno')
            ->get();

        $dataProgramas = [];
        foreach ($pagosProgramas as $pago) {
            $fecha_pago = strtoupper(strtr(date('d/M/Y', strtotime($pago->fecha_pago)), [
                'Jan'=>'ENE','Apr'=>'ABR','Aug'=>'AGO','Dec'=>'DIC'
            ]));
            $dataProgramas[] = [
                'num'            => $num++,
                'recibo'         => $pago->recibo,
                'id_alumno'      => $pago->id_alumno,
                'nombre_programa'=> $pago->nombre_programa ?? 'DESCONOCIDO',
                'periodo'        => $pago->periodo,
                'concepto'       => $pago->concepto,
                'fecha_pago'     => $fecha_pago,
                'monto'          => number_format((float)$pago->monto, 2)
            ];
            $total += (float)$pago->monto;
        }

        $pagosSecundarios = DB::table('pagos_secundarios')
            ->where(function($q) use ($id_corte) {
                $q->where('corte', $id_corte)
                  ->orWhereRaw('CAST(corte AS UNSIGNED) = ?', [$id_corte]);
            })
            ->orderBy('id_alumno')
            ->get();

        $dataSecundarios = [];
        foreach ($pagosSecundarios as $pago) {
            $fecha_pago = strtoupper(strtr(date('d/M/Y', strtotime($pago->fecha_pago)), [
                'Jan'=>'ENE','Apr'=>'ABR','Aug'=>'AGO','Dec'=>'DIC'
            ]));
            $dataSecundarios[] = [
                'num'        => $num++,
                'recibo'     => $pago->recibo,
                'id_alumno'  => $pago->id_alumno,
                'concepto'   => $pago->concepto,
                'periodo'    => $pago->periodo,
                'fecha_pago' => $fecha_pago,
                'monto'      => number_format((float)$pago->monto, 2)
            ];
            $total += (float)$pago->monto;
        }

        return response()->json([
            'pagos_programas'  => $dataProgramas,
            'pagos_secundarios'=> $dataSecundarios,
            'total'            => number_format($total, 2)
        ]);
    }

    public function realizarCorte(Request $request)
{
    try {
        // 1) Validación mínima
        $request->validate([
            'total'    => 'nullable|numeric|min:0',
            'id_autor' => 'nullable|string|max:6',
        ]);

        // 2) Total (si no viene lo calculamos)
        $totalParam = $request->input('total');
        if ($totalParam === null) {
            $pp = $this->scopePendiente(DB::table('pagos_programas'))->sum('monto');
            $ps = $this->scopePendiente(DB::table('pagos_secundarios'))->sum('monto');
            $mi = DB::table('miscelanea')->where('corte', 0)->sum('monto');
            $total = (float)$pp + (float)$ps + (float)$mi;
        } else {
            $total = (float)$totalParam;
        }

        if ($total <= 0) {
            return response()->json(['error' => 'No se realizó el corte porque no hay ingresos registrados'], 400);
        }

        // 3) Autor (string máx 6)
        $id_autor = $request->input('id_autor');
        if (empty($id_autor) && Auth::check()) {
            $id_autor = (string) (Auth::user()->id ?? '1');
        }
        if (empty($id_autor)) {
            $id_autor = '1';
        }
        $id_autor = substr((string)$id_autor, 0, 6);

        // 4) Transacción
        return DB::transaction(function () use ($total, $id_autor) {
            // ⚠️ Si tu PK no es autoincrement, revisa esto
            $id_corte = DB::table('cortes')->insertGetId([
                'fecha'    => now()->format('Y-m-d'),
                'id_autor' => $id_autor,
                'total'    => $total,
            ]);

            // Marcar como cortado
            $this->scopePendiente(DB::table('pagos_programas'))->update(['corte' => $id_corte]);
            $this->scopePendiente(DB::table('pagos_secundarios'))->update(['corte' => $id_corte]);
            DB::table('miscelanea')->where('corte', 0)->update(['corte' => $id_corte]);

            return response()->json([
                'id_corte' => $id_corte,
                'total'    => number_format($total, 2),
                'message'  => 'Corte realizado con éxito',
            ], 200);
        });
    } catch (\Throwable $e) {
        // Log completo y mensaje claro al front
        Log::error('realizarCorte ERROR: '.$e->getMessage(), [
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'error'   => 'Excepción al realizar corte',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    public function miscelanea(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'monto'  => 'required|numeric|min:0'
        ]);

        $ano = date("Y");
        $mes = date("M");
        $dia = date("d");

        $meses = [
            'Jan'=>'ENE','Feb'=>'FEB','Mar'=>'MAR','Apr'=>'ABR','May'=>'MAY','Jun'=>'JUN',
            'Jul'=>'JUL','Aug'=>'AGO','Sep'=>'SEPT','Oct'=>'OCT','Nov'=>'NOV','Dec'=>'DIC'
        ];

        $mesStr = strtoupper($meses[$mes]);
        $periodoHumano = strtoupper("$dia/$mesStr/$ano");
        $fechaYmd      = date("Y-m-d");

        $id = DB::table('miscelanea')->insertGetId([
            'concepto'   => "MISCELANEA",
            'nombre'     => $request->input('nombre'),
            'periodo'    => $periodoHumano,
            'monto'      => $request->input('monto'),
            'fecha_pago' => $fechaYmd,
            'corte'      => 0,
            'recibo'     => 0,
        ]);

        $recibo = "MISC" . str_pad($id, 5, "0", STR_PAD_LEFT);
        DB::table('miscelanea')->where('id',$id)->update(['recibo'=>$recibo]);

        return response()->json(['message' => 'Registro agregado exitosamente'], 201);
    }

    /** Opción A: semanal por rango (sin tabla corte_semanal) */
    public function porSemana(Request $req)
{
    $req->validate([
        'from' => 'nullable|date',
        'to'   => 'nullable|date|after_or_equal:from',
    ]);

    $from = $req->query('from')
        ? Carbon::parse($req->query('from'))->startOfDay()
        : now()->startOfWeek(Carbon::MONDAY)->startOfDay();

    $to = $req->query('to')
        ? Carbon::parse($req->query('to'))->endOfDay()
        : now()->endOfWeek(Carbon::SUNDAY)->endOfDay();

    $rango = [$from->toDateString(), $to->toDateString()];

    $cortes = DB::table('cortes')
        ->whereBetween(DB::raw('DATE(fecha)'), $rango)
        ->orderBy('fecha', 'asc')
        ->get();

    $totalSemana = DB::table('cortes')
        ->whereBetween(DB::raw('DATE(fecha)'), $rango)
        ->sum('total');

    $porDia = DB::table('cortes')
        ->selectRaw('DATE(fecha) as fecha, SUM(total) as total')
        ->whereBetween(DB::raw('DATE(fecha)'), $rango)
        ->groupBy(DB::raw('DATE(fecha)'))
        ->orderBy('fecha')
        ->get();

    return response()->json([
        'from'         => $from->toDateString(),
        'to'           => $to->toDateString(),
        'count'        => $cortes->count(),
        'total_semana' => (float)$totalSemana,
        'por_dia'      => $porDia,
        'cortes'       => $cortes,
    ]);
}

    /** Detalle unificado de un corte (items + totales recalc) */
    public function show($id_corte)
    {
        $corte = DB::table('cortes')->where('id_corte',$id_corte)->first();
        if (!$corte) return response()->json(['error'=>'Corte no encontrado'],404);

        // Programas (permitir varchar con ceros)
        $pp = DB::table('pagos_programas')
            ->where(function($q) use ($id_corte) {
                $q->where('corte', $id_corte)
                  ->orWhereRaw('CAST(corte AS UNSIGNED) = ?', [$id_corte]);
            })
            ->leftJoin('alumnos','alumnos.id_alumno','=','pagos_programas.id_alumno')
            ->leftJoin('programas_predefinidos as prog','prog.id_programa','=','pagos_programas.id_programa')
            ->selectRaw("
                pagos_programas.*,
                COALESCE(alumnos.nombre,'') as alumno,
                COALESCE(prog.nombre, pagos_programas.concepto) as concepto
            ")->get();

        // Secundarios
        $ps = DB::table('pagos_secundarios')
            ->where(function($q) use ($id_corte) {
                $q->where('corte', $id_corte)
                  ->orWhereRaw('CAST(corte AS UNSIGNED) = ?', [$id_corte]);
            })
            ->leftJoin('alumnos','alumnos.id_alumno','=','pagos_secundarios.id_alumno')
            ->selectRaw("
                pagos_secundarios.*,
                COALESCE(alumnos.nombre,'') as alumno
            ")->get();

        $items = [];
        $totalCalc = 0.0;
        $num = 1;

        foreach ($pp as $r) {
            $items[] = $this->normalizaItem('programa', $r, [
                'num' => $num++, 'alumno' => $r->alumno, 'concepto'=>$r->concepto,
                'fecha' => strtoupper(date("d/M/Y", strtotime($r->fecha_pago)))
            ]);
            $totalCalc += (float)$r->monto;
        }

        foreach ($ps as $r) {
            $items[] = $this->normalizaItem('secundario', $r, [
                'num' => $num++, 'alumno' => $r->alumno,
                'fecha' => strtoupper(date("d/M/Y", strtotime($r->fecha_pago)))
            ]);
            $totalCalc += (float)$r->monto;
        }

        // Miscelánea ligada al corte (permitir varchar con ceros)
        $miscelanea = DB::table('miscelanea')
            ->where(function($q) use ($id_corte) {
                $q->where('corte', $id_corte)
                  ->orWhereRaw('CAST(corte AS UNSIGNED) = ?', [$id_corte]);
            })->get();

        foreach ($miscelanea as $m) {
            $items[] = [
                'tipo'     => 'miscelanea',
                'num'      => $num++,
                'recibo'   => $m->recibo ?? '-',
                'alumno'   => '-',
                'concepto' => $m->descripcion ?? 'MISCELÁNEA',
                'periodo'  => $m->periodo ?? '-',
                'fecha'    => $m->created_at ? strtoupper(date("d/M/Y", strtotime($m->created_at))) : "-",
                'monto'    => number_format((float)($m->monto ?? 0), 2),
            ];
            $totalCalc += (float)($m->monto ?? 0);
        }

        // autor legible
        $autor = $corte->id_autor;
        if (is_numeric($autor)) {
            $u = DB::table('usuarios')->where('id',(int)$autor)->first();
            if ($u) $autor = $u->nombre ?: $u->usuario ?: $autor;
        }

        return response()->json([
            'corte' => [
                'id_corte'           => $corte->id_corte,
                'fecha'              => $corte->fecha,
                'autor'              => $autor,
                'total_db'           => (float)$corte->total,
                'total_recalculado'  => round($totalCalc,2),
            ],
            'items' => $items,
        ]);
    }

    /** Listado mensual por query */
    public function mensual(Request $req)
    {
        $year  = (int)$req->query('year', now()->year);
        $month = (int)$req->query('month', now()->month);

        $cortes = DB::table('cortes')
            ->whereYear('fecha',$year)
            ->whereMonth('fecha',$month)
            ->orderBy('fecha','desc')
            ->get();

        return response()->json([
            'year'   => $year,
            'month'  => $month,
            'cortes' => $cortes,
        ]);
    }

    /** -------- REPORTE PDF (detalle del corte) -------- */
    public function reporteDetalle($id_corte, Request $req)
    {
        $info  = DB::table('informacion')->first();
        $corte = DB::table('cortes')->where('id_corte', $id_corte)->first();
        if (!$corte) abort(404, 'Corte no encontrado');

        // Reusa show()
        $detalle = $this->show($id_corte)->getData(true);

        // Logo en Base64 (prioriza tu ruta real)
        $logoData = null;
        $candidatos = [
            public_path('imagenes/mcdclogorecibo.png'), // <-- tu logo
            public_path('imagenes/marcaagua.png'),
            public_path('img/logo.png'),
            public_path('img/logo.jpg'),
            public_path('logo.png'),
            public_path('logo.jpg'),
            storage_path('app/public/logo.png'),
            storage_path('app/public/logo.jpg'),
        ];
        foreach ($candidatos as $p) {
            $rp = realpath($p);
            if ($rp && file_exists($rp)) {
                $ext  = strtolower(pathinfo($rp, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                $logoData = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($rp));
                break;
            }
        }

        $data = [
            'info'       => $info,
            'corte'      => $detalle['corte'],
            'items'      => $detalle['items'],
            'fechaLarga' => $this->fechaLargaES($corte->fecha),
            'folio'      => str_pad($id_corte, 5, '0', STR_PAD_LEFT),
            'logoData'   => $logoData,
            'logoPath'   => $logoData, // compat blades antiguos
        ];

        $pdf = Pdf::loadView('reportes.corte_detalle', $data)->setPaper('letter', 'portrait');

        return $req->query('dl') ? $pdf->download("corte_{$id_corte}.pdf")
                                 : $pdf->stream("corte_{$id_corte}.pdf");
    }

    // --- helper de logo reutilizable (poner en la sección de helpers privados) ---
private function logoData()
{
    $candidatos = [
        public_path('imagenes/mcdlogorecibo.png'), // tu logo actual
        public_path('img/logo.png'),
        public_path('img/logo.jpg'),
        public_path('logo.png'),
        public_path('logo.jpg'),
        storage_path('app/public/logo.png'),
        storage_path('app/public/logo.jpg'),
    ];
    foreach ($candidatos as $p) {
        if (file_exists($p)) {
            $ext  = strtolower(pathinfo($p, PATHINFO_EXTENSION));
            $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
            return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($p));
        }
    }
    return null;
}

// ------------------ REPORTE: SEMANA ------------------
public function reporteSemana(Request $req)
{
    // Reusar el JSON existente
    $json = $this->porSemana($req)->getData(true);

    // Normalizar filas para el blade
    $rows = [];
    $num  = 1;
    $cortes = $json['cortes'] ?? [];

    foreach ($cortes as $c) {
        // $c puede venir como array o como objeto
        $idCorte = is_array($c) ? ($c['id_corte'] ?? null) : ($c->id_corte ?? null);
        $fecha   = is_array($c) ? ($c['fecha'] ?? null)     : ($c->fecha ?? null);
        $autor   = is_array($c) ? ($c['id_autor'] ?? null)  : ($c->id_autor ?? null);
        $total   = is_array($c) ? ($c['total'] ?? 0)        : ($c->total ?? 0);

        if (is_numeric($autor)) {
            $u = DB::table('usuarios')->where('id', (int)$autor)->first();
            if ($u) $autor = $u->nombre ?: $u->usuario ?: $autor;
        }

        $rows[] = [
            'num'   => $num++,
            'folio' => str_pad((int)$idCorte, 5, '0', STR_PAD_LEFT),
            'fecha' => $fecha ? $this->fechaLargaES($fecha) : '-',
            'autor' => $autor ?: '-',
            'total' => (float)$total,
        ];
    }

    $data = [
        'info'     => DB::table('informacion')->first(),
        'rows'     => $rows,
        'total'    => (float)($json['total_semana'] ?? 0),
        'desde'    => $json['from'] ?? null,
        'hasta'    => $json['to'] ?? null,
        'logoPath' => $this->logoData(),
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.corte_semanal', $data)
            ->setPaper('letter','portrait');

    return $req->query('dl')
        ? $pdf->download("cortes_semana_{$data['desde']}_{$data['hasta']}.pdf")
        : $pdf->stream("cortes_semana_{$data['desde']}_{$data['hasta']}.pdf");
}



// ------------------ REPORTE: MES ------------------
public function reporteMes($anio, $mes, Request $req)
{
    $crudos = DB::table('cortes')
        ->whereYear('fecha', $anio)
        ->whereMonth('fecha', $mes)
        ->orderBy('fecha','asc')
        ->get();

    $rows = [];
    $totalMes = 0.0;
    foreach ($crudos as $c) {
        $autor = $c->id_autor;
        if (is_numeric($autor)) {
            $u = DB::table('usuarios')->where('id',(int)$autor)->first();
            if ($u) $autor = $u->nombre ?: $u->usuario ?: $autor;
        }
        $rows[] = [
            'folio' => str_pad($c->id_corte, 5, '0', STR_PAD_LEFT),
            'fecha' => $this->fechaLargaES($c->fecha),
            'autor' => $autor,
            'total' => (float)$c->total,
        ];
        $totalMes += (float)$c->total;
    }

    $info = DB::table('informacion')->first();
    $data = [
        'info'      => $info,
        'logoPath'  => $this->logoData(),
        'anio'      => (int)$anio,
        'mes'       => (int)$mes,
        'mesNombre' => $this->monthName($mes),
        'rows'      => $rows,
        'totalMes'  => $totalMes,
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.corte_mes', $data)->setPaper('letter','portrait');
    return $req->query('dl') ? $pdf->download("corte_mes_{$anio}_{$mes}.pdf")
                             : $pdf->stream("corte_mes.pdf");
}

// ------------------ REPORTE: AÑO ------------------
public function reporteAnio($anio, Request $req)
{
    $grupos = DB::table('cortes')
        ->selectRaw('MONTH(fecha) as mes, SUM(total) as total')
        ->whereYear('fecha', $anio)
        ->groupBy(DB::raw('MONTH(fecha)'))
        ->orderBy(DB::raw('MONTH(fecha)'))
        ->get();

    $rows = [];
    $totalAnual = 0.0;
    foreach ($grupos as $g) {
        $rows[] = [
            'mes'   => (int)$g->mes,
            'nombre'=> $this->monthName($g->mes),
            'total' => (float)$g->total,
        ];
        $totalAnual += (float)$g->total;
    }

    $info = DB::table('informacion')->first();
    $data = [
        'info'       => $info,
        'logoPath'   => $this->logoData(),
        'anio'       => (int)$anio,
        'rows'       => $rows,
        'totalAnual' => $totalAnual,
    ];

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.corte_anio', $data)->setPaper('letter','portrait');
    return $req->query('dl') ? $pdf->download("corte_anual_{$anio}.pdf")
                             : $pdf->stream("corte_anual.pdf");
}

}
