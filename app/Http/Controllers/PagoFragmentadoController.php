<?php

namespace App\Http\Controllers;

use App\Models\PagoFragmentado;
use Illuminate\Http\Request;

class PagoFragmentadoController extends Controller
{
    // Listar todos
    public function index()
    {
        return PagoFragmentado::all();
    }

    // Registrar uno
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alumno' => 'required',
            'id_programa' => 'required',
            'id_clase' => 'required',
            'periodo' => 'required',
            'id_maestro' => 'required',
            'monto' => 'required|numeric',
            'nomina' => 'nullable|string',
        ]);

        $pago = PagoFragmentado::create($validated);
        return response()->json($pago, 201);
    }

    // Consultar uno por id
    public function show($id)
    {
        return PagoFragmentado::findOrFail($id);
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $pago = PagoFragmentado::findOrFail($id);
        $pago->update($request->all());
        return response()->json($pago, 200);
    }

    // Eliminar
    public function destroy($id)
    {
        $pago = PagoFragmentado::findOrFail($id);
        $pago->delete();
        return response()->json(null, 204);
    }
}
