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
        $adeudo->update($request->all());
        return response()->json($adeudo, 200);
    }

    public function destroy($id)
    {
        $adeudo = AdeudoFragmentado::findOrFail($id);
        $adeudo->delete();
        return response()->json(null, 204);
    }
}
