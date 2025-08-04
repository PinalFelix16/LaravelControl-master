<?php

namespace App\Http\Controllers;

use App\Models\PagoPrograma;
use Illuminate\Http\Request;

class PagoProgramaController extends Controller
{
    // Listar todos los pagos de programas
    public function index(Request $request)
    {
    $query = \App\Models\PagoPrograma::query();

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
    if ($request->filled('fecha_pago')) {
        $query->whereDate('fecha_pago', $request->fecha_pago);
    }

    return $query->get();
    }


    // Registrar un pago de programa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alumno' => 'required',
            'id_programa' => 'required',
            'periodo' => 'required',
            'concepto' => 'required',
            'monto' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'beca' => 'nullable|numeric',
            'fecha_limite' => 'nullable|date',
            'fecha_pago' => 'nullable|date',
            'recibo' => 'nullable|string',
            'corte' => 'nullable|string',
        ]);

        $pago = PagoPrograma::create($validated);
        return response()->json($pago, 201);
    }

    // Consultar un pago de programa específico
    public function show($id)
    {
        return PagoPrograma::findOrFail($id);
    }

    // Actualizar un pago de programa
    public function update(Request $request, $id)
    {
        $pago = PagoPrograma::findOrFail($id);
        $pago->update($request->all());
        return response()->json($pago, 200);
    }

    // Eliminar un pago de programa
    public function destroy($id)
    {
        $pago = PagoPrograma::findOrFail($id);
        $pago->delete();
        return response()->json(null, 204);
    }
}
