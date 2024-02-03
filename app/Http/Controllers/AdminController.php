<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
    protected function getAll(Request $request)
    {
        $admin = Admin::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => 'success',
            'code' => '200',
            'data' => $admin
        ];

        return response()->json($msg);
    }

    protected function getOne(Request $request, $email)
    {
        $admin = Admin::where('email',$email)->first();
        if (!$admin) {
            $msg = [
                'msg' => 'Admin no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Admin Encontrado',
            'status' => 'success',
            'code' => '200',
            'data' => $admin
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

        $existingAdmin = Admin::where('email',$email)->first();
        
        
        if (!$existingAdmin) {

            if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {

                $admin = new Admin();
                $admin->email = $email;
                $admin->password = Hash::make($request->password);
                $admin->save();

                $msg = [
                    'msg' => 'Nuevo administrador creado con éxito',
                    'status' => 'success',
                    'code' => '201',
                    'data' => $admin
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

            'msg' => 'Este administrador ya existe',
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
        $admin = Admin::where('email',$email)->first();

        if($admin){
           $oldPassword = $admin->password; 
           if((!Hash::check($newPassword, $oldPassword))){
                $admin->password = Hash::make($newPassword);
                $admin->save();
                $msg = [
                    'msg' => 'Contraseña actualizada correctamente',
                    'status' => 'success',
                    'code' => '200',
                    'data' => $admin
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
            'msg' => 'No se encontró al administrador',
            'status' => 'failed',
            'code' => '400',
        ];
        return response($msg);
    }

    protected function delete(Request $request, $email)
    {
        $admin=Admin::where('email',$email)->first();

        if (!$admin) {
            $msg = [
                'msg' => 'Usuario no encontrado',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }

        $admin->delete();
        $msg = [
            'msg' => 'Usuario eliminado correctamente',
            'status' => 'success',
            'code' => '200',
            'data' => $admin
        ];

        return response()->json($msg);

    }
}
