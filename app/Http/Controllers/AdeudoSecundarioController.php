<?php

namespace App\Http\Controllers;

use App\Models\AdeudoSecundario;
use Illuminate\Http\Request;

class AdeudoSecundarioController extends Controller
{
    public function index()
    {
        return AdeudoSecundario::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alumno' => 'required',
            'concepto' => 'required|string',
            'periodo' => 'required',
            'monto' => 'required|numeric',
            'descuento' => 'nullable|numeric',
            'corte' => 'nullable|string',
        ]);

        $adeudo = AdeudoSecundario::create($validated);
        return response()->json($adeudo, 201);
    }

    public function show($id)
    {
        return AdeudoSecundario::findOrFail($id);
    }

public function update(Request $request, $id)
{
    $adeudo = AdeudoSecundario::findOrFail($id);
    $validated = $request->validate([
        'id_alumno'  => 'sometimes|required',
        'concepto'   => 'sometimes|required|string',
        'periodo'    => 'sometimes|required',
        'monto'      => 'sometimes|required|numeric',
        'descuento'  => 'nullable|numeric',
        'corte'      => 'nullable|string',
    ]);
    $adeudo->update($validated);
    return response()->json($adeudo, 200);
}


    public function destroy($id)
    {
        $adeudo = AdeudoSecundario::findOrFail($id);
        $adeudo->delete();
        return response()->json(null, 204);
    }
}
