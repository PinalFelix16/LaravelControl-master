<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    public function index()
    {
        return Pago::with('alumno')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'concepto' => 'required|string',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'forma_pago' => 'nullable|string',
            'referencia' => 'nullable|string'
        ]);

        $pago = Pago::create($validated);

        return response()->json($pago, 201);
    }

    public function show($id)
    {
        return Pago::with('alumno')->findOrFail($id);
    }

    public function destroy($id)
    {
        Pago::destroy($id);
        return response()->json(null, 204);
    }

    public function update(Request $request, $id)
    {
    // Busca el pago o falla con 404
    $pago = Pago::findOrFail($id);

    // Valida solo los campos que envíes (sometimes|required)
    $data = $request->validate([
        'alumno_id'  => 'sometimes|required|exists:alumnos,id',
        'concepto'   => 'sometimes|required|string',
        'monto'      => 'sometimes|required|numeric',
        'fecha_pago' => 'sometimes|required|date',
        'forma_pago' => 'nullable|string',
        'referencia' => 'nullable|string',
    ]);

    // Actualiza y devuelve JSON
    $pago->update($data);
    return response()->json($pago, 200);
    }

    public function generarReciboPDF($id)
    {
        $pago = Pago::with('alumno')->findOrFail($id);

        $pdf = Pdf::loadView('recibos.recibo', compact('pago'));
        return $pdf->download('recibo_pago_' . $pago->id . '.pdf');
    }
}
