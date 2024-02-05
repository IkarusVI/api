<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Host;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HostController extends Controller
{
    
    protected function getAll(Request $request)
    {
        $host = Host::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => 'success',
            'code' => '200',
            'data' => $host
        ];

        return response()->json($msg);
    }

    protected function getOne(Request $request, $id)
    {
        $host = Host::where('id',$id)->first();
        if (!$host) {
            $msg = [
                'msg' => 'Host no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Host Encontrado',
            'status' => 'success',
            'code' => '200',
            'data' => $host
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

        $existingHostMail = Host::where('email',$email)->first();

        $userName =$request->userName;

        $existingHostUserName = Host::where('userName',$userName)->first();
        
        
        if (!$existingHostMail && !$existingHostUserName) {

            if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {

                $host = new Host();
                $host->email = $email;
                $host->userName = $userName;
                $host->password = Hash::make($request->password);
                $host->save();

                $msg = [
                    'msg' => 'Nuevo host creado con éxito',
                    'status' => 'success',
                    'code' => '201',
                    'data' => $host
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

            'msg' => 'Este host ya existe',
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
        $host = Host::where('email',$email)->first();

        if($host){
           $oldPassword = $host->password; 
           if((!Hash::check($newPassword, $oldPassword))){
            $host->password = Hash::make($newPassword);
            $host->save();
                $msg = [
                    'msg' => 'Contraseña actualizada correctamente',
                    'status' => 'success',
                    'code' => '200',
                    'data' => $host
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
            'msg' => 'No se encontró al host',
            'status' => 'failed',
            'code' => '400',
        ];
        return response($msg);
    }
    protected function delete(Request $request, $id)
    {
        $host=Host::where('id',$id)->first();

        if (!$host) {
            $msg = [
                'msg' => 'Host no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg, 404);
        }
        $host->delete();
        $msg = [
            'msg' => 'Host eliminado correctamente',
            'status' => 'success',
            'code' => '200',
            'data' => $host
        ];

        return response()->json($msg);

    }
}

