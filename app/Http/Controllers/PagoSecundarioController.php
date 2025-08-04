<?php

namespace App\Http\Controllers;

use App\Models\PagoSecundario;
use Illuminate\Http\Request;

class PagoSecundarioController extends Controller
{
    // Listar todos
    public function index()
    {
        return PagoSecundario::all();
    }

    // Registrar uno
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alumno' => 'required',
            'concepto' => 'required|string',
            'periodo' => 'required',
            'monto' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'fecha_pago' => 'nullable|date',
            'nomina' => 'nullable|string',
            'recibo' => 'nullable|string',
            'corte' => 'nullable|string',
        ]);

        $pago = PagoSecundario::create($validated);
        return response()->json($pago, 201);
    }

    // Consultar uno por id
    public function show($id)
    {
        return PagoSecundario::findOrFail($id);
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $pago = PagoSecundario::findOrFail($id);
        $pago->update($request->all());
        return response()->json($pago, 200);
    }

    // Eliminar
    public function destroy($id)
    {
        $pago = PagoSecundario::findOrFail($id);
        $pago->delete();
        return response()->json(null, 204);
    }
}
