<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    // Listar todos los pagos (con datos del alumno)
    public function index()
    {
        return Pago::with('alumno')->get();
    }

    // Crear un nuevo pago
    public function store(Request $request)
    {
        $validated = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'concepto' => 'required|string|max:255',
            'monto' => 'required|numeric',
            'fecha_pago' => 'required|date',
            'forma_pago' => 'nullable|string|max:255',
            'referencia' => 'nullable|string|max:255'
        ]);

        $pago = Pago::create($validated);

        return response()->json($pago, 201);
    }

    // Mostrar un solo pago (por id, con datos del alumno)
    public function show($id)
    {
        return Pago::with('alumno')->findOrFail($id);
    }

    // Actualizar un pago existente
    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);

        $data = $request->validate([
            'alumno_id'  => 'sometimes|required|exists:alumnos,id',
            'concepto'   => 'sometimes|required|string|max:255',
            'monto'      => 'sometimes|required|numeric',
            'fecha_pago' => 'sometimes|required|date',
            'forma_pago' => 'nullable|string|max:255',
            'referencia' => 'nullable|string|max:255',
        ]);

        $pago->update($data);

        return response()->json($pago, 200);
    }

    // Eliminar un pago
    public function destroy($id)
    {
        $pago = Pago::findOrFail($id);
        $pago->delete();
        return response()->json(['message' => 'Pago eliminado'], 200);
    }

    // Descargar el recibo PDF del pago
    public function generarReciboPDF($id)
    {
        $pago = Pago::with('alumno')->findOrFail($id);
        $pdf = Pdf::loadView('recibos.recibo', compact('pago'));
        return $pdf->download('recibo_pago_' . $pago->id . '.pdf');
    }
}

