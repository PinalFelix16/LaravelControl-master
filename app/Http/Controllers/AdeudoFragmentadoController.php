<?php

namespace App\Http\Controllers;

use App\Models\AdeudoFragmentado;
use Illuminate\Http\Request;

class AdeudoFragmentadoController extends Controller
{
    public function index()
    {
        return AdeudoFragmentado::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_alumno' => 'required',
            'id_programa' => 'required',
            'id_clase' => 'required',
            'periodo' => 'required',
            'id_maestro' => 'required',
            'monto' => 'required|numeric',
        ]);

        $adeudo = AdeudoFragmentado::create($validated);
        return response()->json($adeudo, 201);
    }

    public function show($id)
    {
        return AdeudoFragmentado::findOrFail($id);
    }

public function update(Request $request, $id)
{
    $adeudo = AdeudoFragmentado::findOrFail($id);
    $validated = $request->validate([
        'id_alumno'   => 'sometimes|required',
        'id_programa' => 'sometimes|required',
        'id_clase'    => 'sometimes|required',
        'periodo'     => 'sometimes|required',
        'id_maestro'  => 'sometimes|required',
        'monto'       => 'sometimes|required|numeric',
    ]);
    $adeudo->update($validated);
    return response()->json($adeudo, 200);
}


    public function destroy($id)
    {
        $adeudo = AdeudoFragmentado::findOrFail($id);
        $adeudo->delete();
        return response()->json(null, 204);
    }
}
