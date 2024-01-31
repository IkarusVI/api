<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Guest;

class GuestController extends Controller
{
     
    protected function getAll(Request $request)
    {
        $guest = Guest::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => 'success',
            'code' => '200',
            'data' => $guest
        ];

        return response()->json($msg);
    }

    protected function getOne(Request $request, $email)
    {
        $guest = Guest::where('email',$email)->first();
        if (!$guest) {
            $msg = [
                'msg' => 'Guest no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Guest Encontrado',
            'status' => 'success',
            'code' => '200',
            'data' => $guest
        ];

        return response()->json($msg); 
    }

    protected function create(Request $request)
    {
        if($request->password==null || $request->email==null){
            $msg = [
                'msg' => 'Uno o mas campos vacios',
                'status' => 'failed',
                'code' => '201',
            ];

            return response()->json($msg);
        }
        $email =$request->email;

        $existingGuest = Guest::where('email',$email)->first();
        
        
        if (!$existingGuest) {

            if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {

                $guest = new Guest();
                $guest->email = $email;
                $guest->password = $request->password;
                $guest->save();

                $msg = [
                    'msg' => 'Nuevo guest creado con éxito',
                    'status' => 'success',
                    'code' => '201',
                    'data' => $guest
                ];
                return response()->json($msg);

            } 
    
            $msg= [
                'msg' => 'Email no es válido',
                'status' => 'failed',
                'code' => '400'
            ];
    
            return response()->json($msg);
            
        }
        $msg = [

            'msg' => 'Este guest ya existe',
            'status' => 'failed',
            'code' => '400',
        ];
        return response()->json($msg);
     
    }

    protected function modify(Request $request, $email)
    {
        $newPassword = $request->input('password');
        $guest = Guest::where('email',$email)->first();

        if($guest){
           $oldPassword = $guest->password; 
           if($oldPassword != $newPassword){
                $guest->password = $newPassword;
                $guest->save();
                $msg = [
                    'msg' => 'Contraseña actualizada correctamente',
                    'status' => 'success',
                    'code' => '200',
                    'data' => $guest
                ];
                return response()->json($msg);
           }
           $msg = [
            'msg' => 'Contraseña no se cambio ya que son identicas',
            'status' => 'failed',
            'code' => '400',
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'No se encontró al guest',
            'status' => 'failed',
            'code' => '400',
        ];
        return response($msg);
    }

    protected function delete(Request $request, $email)
    {
        $guest=Guest::where('email',$email)->first();

        if (!$guest){
            $msg = [
                'msg' => 'Host no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }
        $guest->delete();
        $msg = [
            'msg' => 'Guest eliminado correctamente',
            'status' => 'success',
            'code' => '200',
            'data' => $guest
        ];

        return response()->json($msg);

    }
}
