<?php

namespace App\Http\Controllers;

use App\Models\AdeudoPrograma;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdeudoProgramaController extends Controller
{
    // Listar todos
    public function index(Request $request)
    {
    $query = \App\Models\AdeudoPrograma::query();

    if ($request->filled('alumno')) {
        $query->where('id_alumno', $request->alumno);
    }
    if ($request->filled('periodo')) {
        $query->where('periodo', $request->periodo);
    }
    if ($request->filled('concepto')) {
        $query->where('concepto', $request->concepto);
    }
    if ($request->filled('programa')) {
        $query->where('id_programa', $request->programa);
    }
    if ($request->filled('fecha_limite')) {
        $query->whereDate('fecha_limite', $request->fecha_limite);
    }

    return $query->get();
    }

    // Registrar uno
    public function store(Request $request)
{
    $data = $request->all();

    // Si no se envía fecha_limite, se genera automáticamente: día 5 del mes de 'periodo'
    if (empty($data['fecha_limite']) && !empty($data['periodo'])) {
        // Si periodo viene en formato 'YYYY-MM'
        $anio_mes = explode('-', $data['periodo']);
        if (count($anio_mes) === 2) {
            $anio = $anio_mes[0];
            $mes = $anio_mes[1];
            $data['fecha_limite'] = "$anio-$mes-05";
        }
    }

    $validated = validator($data, [
        'id_alumno'   => 'required',
        'id_programa' => 'required',
        'periodo'     => 'required',
        'concepto'    => 'required|string',
        'monto'       => 'required|numeric',
        'beca'        => 'nullable|numeric',
        'descuento'   => 'nullable|numeric',
        'fecha_limite'=> 'required|date',
    ])->validate();

    $adeudo = \App\Models\AdeudoPrograma::create($validated);
    return response()->json($adeudo, 201);
}


    // Consultar uno por id
    public function show($id)
    {
        return AdeudoPrograma::findOrFail($id);
    }

    // Actualizar
public function update(Request $request, $id)
{
    $adeudo = AdeudoPrograma::findOrFail($id);
    $validated = $request->validate([
        'id_alumno'   => 'sometimes|required',
        'id_programa' => 'sometimes|required',
        'periodo'     => 'sometimes|required',
        'concepto'    => 'sometimes|required|string',
        'monto'       => 'sometimes|required|numeric',
        'beca'        => 'nullable|numeric',
        'descuento'   => 'nullable|numeric',
        'fecha_limite'=> 'sometimes|required|date',
    ]);
    $adeudo->update($validated);
    return response()->json($adeudo, 200);
}


    // Eliminar
    public function destroy($id)
    {
        $adeudo = AdeudoPrograma::findOrFail($id);
        $adeudo->delete();
        return response()->json(null, 204);
    }

        public function exportarPDF()
    {
        $hoy = now()->toDateString();

        // Mostrar solo adeudos vencidos (fecha_límite < hoy)
        $adeudos = AdeudoPrograma::with('alumno')
            ->where('fecha_limite', '<', $hoy)
            ->where('monto', '>', 0)
            ->get();

            // Suma recargos por cada adeudo
    foreach ($adeudos as $adeudo) {
        $total_recargos = \App\Models\AdeudoSecundario::where('id_alumno', $adeudo->id_alumno)
            ->where('concepto', 'RECARGO')
            ->where('periodo', $adeudo->periodo)
            ->sum('monto');
        $adeudo->total_recargos = $total_recargos;
        $adeudo->total_a_pagar = $adeudo->monto + $total_recargos;
    }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('adeudos.pdf', compact('adeudos'));

        return $pdf->download('reporte_adeudos.pdf');
    }


}
