<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email:rfc',
            'password' => 'required'
        ]);

        if (Auth::guard('host')->attempt($credentials)) {
            return Auth::guard('host')->user()->createToken("token");
        }

        if (Auth::guard('guest')->attempt($credentials)) {
            return Auth::guard('guest')->user()->createToken("token");
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            return Auth::guard('admin')->user()->createToken("token");
        }

        return 'Correo electrónico o contraseña incorrectos';
    }

    protected function identify(Request $request)
    {
        $user = $request->user;
        return response($user);   
    }

    protected function killToken(Request $request)
    {
        // Obtener el usuario autenticado adjuntado al objeto $request
        $user = $request->user;
    
        // Verificar si el usuario está autenticado
        if ($user) {
            // Cerrar la sesión del usuario y revocar todos los tokens
            $user->tokens()->delete();
    
            // Devolver un mensaje de éxito
            return response()->json(['msg' => 'Sesión cerrada y tokens eliminados con éxito']);
        } else {
            // Devolver un mensaje de error si no hay usuario autenticado
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }
}