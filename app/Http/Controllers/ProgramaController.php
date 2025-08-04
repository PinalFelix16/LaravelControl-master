<?php


// app/Http/Controllers/ProgramaController.php
namespace App\Http\Controllers;

use App\Models\Programa;
use Illuminate\Http\Request;

class ProgramaController extends Controller
{
    public function index() {
        return Programa::all();
    }

    public function show($id) {
        return Programa::findOrFail($id);
    }

    public function store(Request $request) {
        $programa = Programa::create($request->all());
        return response()->json($programa, 201);
    }

    public function update(Request $request, $id) {
        $programa = Programa::findOrFail($id);
        $programa->update($request->all());
        return response()->json($programa, 200);
    }

    public function destroy($id) {
        Programa::destroy($id);
        return response()->json(null, 204);
    }
}
