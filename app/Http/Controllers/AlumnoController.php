<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function index()
    {
        return Alumno::all();
    }

    public function store(Request $request)
    {
        $alumno = Alumno::create($request->all());
        return response()->json($alumno, 201);
    }

    public function show($id)
    {
        return Alumno::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);
        $alumno->update($request->all());
        return response()->json($alumno, 200);
    }

    public function destroy($id)
    {
        Alumno::destroy($id);
        return response()->json(null, 204);
    }
}
