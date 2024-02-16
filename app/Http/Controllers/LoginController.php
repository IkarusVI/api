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
            'email' => 'sometimes|required_without:username|email:rfc',
            'username' => 'sometimes|required_without:email',
            'password' => 'required'
        ]);

        $token = $request->bearerToken();
        
        if(!$token || !Auth::guard('sanctum')->check()){

            $guards = ['host', 'guest', 'admin'];

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->attempt($credentials)) {
                    return Auth::guard($guard)->user()->createToken("token");
                }
            }

            $msg = [
                'msg' => 'Credenciales Incorrectas',
                'status' => 'failed',
                'code'=> 400
            ];
            
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Ya estás logueado',
            'status' => 'failed',
            'code'=> 400
        ];
        return response()->json($msg);
    }
    
    protected function identify(Request $request)
    {
        $user = $request->user;
        return response($user);   
    }
    
    protected function killToken(Request $request)
    {
        $user = $request->user;
        if ($user) {
            $user->tokens()->delete();
            $msg = [
                'msg' => 'Sesión cerrada y tokens eliminados',
                'status' => 'sucess',
                'code'=> 200
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Usuario no autenticado',
            'status' => 'failed',
            'code'=> 400
        ];
        return response()->json($msg);
    }
}