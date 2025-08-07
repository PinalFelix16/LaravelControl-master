<?php

namespace App\Http\Controllers;

use App\Models\Miscelanea;
use Illuminate\Http\Request;

class MiscelaneaController extends Controller
{
    public function index()
    {
        return response()->json(Miscelanea::where('corte', 0)->get());
    }

    public function store(Request $request)
    {
        // Validar campos
        $data = $request->validate([
            'descripcion' => 'required|string',
            'monto'       => 'required|numeric',
            // 'corte'    => 'required|boolean', // Opcional, lo forzamos abajo
        ]);

        // Si no mandan 'corte', ponlo en 0 (no realizado)
        $data['corte'] = $request->input('corte', 0);

        return response()->json(Miscelanea::create($data), 201);
    }
}
