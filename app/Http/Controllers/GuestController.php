<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    protected function getOne(Request $request, $id)
    {
        $guest = Guest::where('id',$id)->first();
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
        if($request->password==null || $request->email==null || $request->userName==null){
            $msg = [
                'msg' => 'Uno o mas campos vacios',
                'status' => 'failed',
                'code' => '201',
            ];

            return response()->json($msg);
        }
        $email =$request->email;

        $existingGuestMail = Guest::where('email',$email)->first();

        $userName =$request->userName;

        $existingGuestUserName = Guest::where('userName',$userName)->first();
        
        
        if (!$existingGuestMail && !$existingGuestUserName) {

            if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {

                $guest = new Guest();
                $guest->email = $email;
                $guest->userName = $userName;
                $guest->password = Hash::make($request->password);
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
        
        $validator = Validator::make($request->all(), [
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            $msg = [
                'msg' => 'La contraseña es requerida',
                'status' => 'failed',
                'code' => '400',
            ];
            return response()->json($msg);
        }

        $newPassword = $request->input('password');
        $guest = Guest::where('email',$email)->first();

        if($guest){
           $oldPassword = $guest->password; 
           if((!Hash::check($newPassword, $oldPassword))){
            $guest->password = Hash::make($newPassword);
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

    protected function delete(Request $request, $id)
    {
        $guest=Guest::where('id',$id)->first();

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
