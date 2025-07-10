<?php

namespace App\Http\Controllers;

use App\Models\ProgramaPredefinido;
use Illuminate\Http\Request;

class ProgramaPredefinidoController extends Controller
{
    public function index()
    {
        return ProgramaPredefinido::all();
    }

    public function store(Request $request)
    {
        $programa = ProgramaPredefinido::create($request->all());
        return response()->json($programa, 201);
    }

    public function show($id)
    {
        return ProgramaPredefinido::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $programa = ProgramaPredefinido::findOrFail($id);
        $programa->update($request->all());
        return response()->json($programa, 200);
    }

    public function destroy($id)
    {
        ProgramaPredefinido::destroy($id);
        return response()->json(null, 204);
    }
}
