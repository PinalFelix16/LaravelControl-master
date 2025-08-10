<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rol;

class RolController extends Controller {
  public function index() { return Rol::orderBy('nombre')->paginate(20); }
  public function store(Request $r) {
    $data = $r->validate(['nombre'=>'required|string|max:60|unique:roles,nombre','descripcion'=>'nullable|string|max:255']);
    return Rol::create($data);
  }
  public function show($id) { return Rol::findOrFail($id); }
  public function update(Request $r, $id) {
    $rol = Rol::findOrFail($id);
    $data = $r->validate(['nombre'=>"required|string|max:60|unique:roles,nombre,{$rol->id_rol},id_rol",'descripcion'=>'nullable|string|max:255']);
    $rol->update($data); return $rol;
  }
  public function destroy($id) { Rol::findOrFail($id)->delete(); return response()->noContent(); }
}
