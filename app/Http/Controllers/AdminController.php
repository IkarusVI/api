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
            'status' => '200',
            'data' => $admin
        ];

        return response()->json($msg);
    }

    protected function getOne(Request $request, $id)
    {
        $admin = Admin::where('id',$id)->first();
        if (!$admin) {
            $msg = [
                'msg' => 'Admin no encontrado',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Admin Encontrado',
            'status' => '200',
            'data' => $admin
        ];

        return response()->json($msg); 
    }

    protected function create(Request $request)
    {
        if($request->password==null || $request->email==null || $request->userName==null){
            $msg = [
                'msg' => 'Uno o mas campos vacios',
                'status' => '400',
            ];

            return response()->json($msg);
        }
        $email =$request->email;

        $existingAdminMail = Admin::where('email',$email)->first();

        $userName =$request->userName;

        $existingAdminUserName = Admin::where('userName',$userName)->first();
        
        
        if (!$existingAdminMail && !$existingAdminUserName) {

            if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {

                $admin = new Admin();
                $admin->email = $email;
                $admin->userName = $userName;
                $admin->password = Hash::make($request->password);
                $admin->save();

                $msg = [
                    'msg' => 'Nuevo admin creado con éxito',
                    'status' => '200',
                    'data' => $admin
                ];
                return response()->json($msg);
            } 
    
            $msg= [
                'msg' => 'Email no es válido',
                'status' => '400',
            ];
    
            return response()->json($msg);
            
        }
        $msg = [

            'msg' => 'Este admin ya existe',
            'status' => '400',
        ];
        return response()->json($msg);
     
    }

    protected function modify(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'password' =>   'required_without_all:userName,email',
            'userName' =>   'required_without_all:password,email',
            'email'    =>   'required_without_all:password,userName|email',
        ]);

        if ($validator->fails()) {
            $msg = [
                'msg' => 'Debe proporcionar al menos un campo',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        $admin = $request->user;

        if (!$admin) {
            $msg = [
                'msg' => 'No se encontró al administrador',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        if ($request->filled('password')) {
            $newPassword = $request->input('password');
            $oldPassword = $admin->password;

            if (Hash::check($newPassword, $oldPassword)) {
                $msg = [
                    'msg' => 'La contraseña no ha cambiado ya que es idéntica a la anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }

            $admin->password = Hash::make($newPassword);
        }

        if ($request->filled('userName')) {
            $newUsername = $request->input('userName');
            if ($newUsername === $admin->userName) {
                $msg = [
                    'msg' => 'El username no ha cambiado ya que es idéntico al anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
            $admin->userName = $newUsername;
        }

        if ($request->filled('email')) {
            $newEmail = $request->input('email');
            if ($newEmail === $admin->email) {
                $msg = [
                    'msg' => 'El email no ha cambiado ya que es idéntico al anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
            $admin->email = $newEmail;
        }

        $admin->save();

        $msg = [
            'msg' => 'Datos actualizados correctamente',
            'status' => '200',
            'data' => $admin
        ];
        
        return response()->json($msg);
            
        
    }

    protected function delete(Request $request, $id)
    {
        $admin=Admin::where('id',$id)->first();

        if (!$admin) {
            $msg = [
                'msg' => 'Usuario no encontrado',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $admin->delete();
        $msg = [
            'msg' => 'Usuario eliminado correctamente',
            'status' => '200',
            'data' => $admin
        ];

        return response()->json($msg);

    }
}
