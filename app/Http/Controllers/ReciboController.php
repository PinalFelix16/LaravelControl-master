<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboController extends Controller
{
    /**
     * Genera el recibo PDF de un pago específico
     */
    public function generarRecibo($id)
    {
        // Asegúrate de cargar la relación con el alumno
        $pago = Pago::with('alumno')->findOrFail($id);

        // Generar el PDF a partir de la vista y los datos
        $pdf = Pdf::loadView('recibos.recibo', compact('pago'));

        // Mostrar en navegador
        return $pdf->stream('recibo_pago_' . $pago->id . '.pdf');

        // Opcional: descarga automática
        //return $pdf->download('recibo_pago_' . $pago->id . '.pdf');


    }
}
