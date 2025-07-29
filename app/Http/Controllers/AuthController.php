<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Maneja el inicio de sesión permitiendo usar email o username
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Intentar primero con email
        $credentials = ['email' => $request->email, 'password' => $request->password];
        if (!Auth::attempt($credentials)) {
            // Si falla, intentar con name
            $credentials = ['name' => $request->email, 'password' => $request->password];
            if (!Auth::attempt($credentials)) {
                return response()->json(['success' => false, 'message' => 'Credenciales incorrectas'], 401);
            }
        }

        $user = Auth::user();

        return response()->json([
            'success' => true,
            'message' => 'Login correcto',
            'user_id' => $user->id,
            'user_type' => $user->rol ?? 'user',
            'name' => $user->name ?? $user->email,
            'rol' => $user->rol ?? 'N/A'
        ]);
    }
}
