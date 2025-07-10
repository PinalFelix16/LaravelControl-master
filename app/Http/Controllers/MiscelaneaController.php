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
        $data = $request->validate([
            'descripcion' => 'required|string',
            'monto'       => 'required|numeric',
            'corte'       => 'required|boolean',
        ]);

        return response()->json(Miscelanea::create($data), 201);
    }
}
