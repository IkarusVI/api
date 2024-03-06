<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Guest;
use App\Models\Host;

class LoginController extends Controller
{
    protected function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'sometimes|required_without:userName|email:rfc',
            'userName' => 'sometimes|required_without:email',
            'password' => 'required'
        ]);

        $token = $request->bearerToken();
        
        if(!$token || !Auth::guard('sanctum')->check()){

            $guards = ['host', 'guest', 'admin'];

            foreach ($guards as $guard) {
                if (Auth::guard($guard)->attempt($credentials)) {
                    $token = Auth::guard($guard)->user()->createToken("token")->plainTextToken;

                    $msg = [
                        'msg' => 'Bienvenido',
                        'status' => '200',
                        'data'=> $token
                    ];
                    return $msg;

                }
            }

            $msg = [
                'msg' => 'Credenciales Incorrectas',
                'status' => '400',
            ];
            
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Ya estás logueado',
            'status' => '400',
        ];
        return response()->json($msg);
    }
    
    protected function identify(Request $request)
    {
        $user = $request->user;
        $class =$request->class;

        $msg = [
            'msg' => $class,
            'status' => '200',
            'data' => $user
        ];
        return response($msg);   
    }
    
    protected function killToken(Request $request)
    {
        $user = $request->user;
        if ($user) {
            $user->tokens()->delete();
            $msg = [
                'msg' => 'Sesión cerrada y tokens eliminados',
                'status' => '200',
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Usuario no autenticado',
            'status' => '400',
        ];
        return response()->json($msg);
    }


    /*
    protected function redirect(Request $request){
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['url' => $url]);
    }

    public function callback(Request $request){
        $user = Socialite::driver('google')->stateless()->user();
    
        $existingGuest = Guest::where('email', $user->email)
        ->orWhere('googleId', $user->id)
        ->first();
        

        if(!$existingGuest){
            $newGuest = new Guest();
            $newGuest->userName = $user->name;
            $newGuest->email = $user->email;
            $newGuest->googleId = $user->id;
            $newGuest->save();

            $msg = [
                'msg' => 'Usuario registrado con éxito',
                'status' => '200',
            ];
            return $msg;
        }
            
        Auth::login($existingGuest);
        $token = $existingGuest->createToken("token")->plainTextToken;
           
        $msg = [
            'msg' => 'Bienvenido',
            'status' => '200',
            'data'=> $token
        ];
        return $msg;

    }    

    */
}