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

    protected function modify(Request $request, $id=null)
    {
        $validator = Validator::make($request->all(), [
            'password' =>   'required_without_all:username,email',
            'username' =>   'required_without_all:password,email',
            'email'    =>   'required_without_all:password,username|email',
        ]);
    
        if ($validator->fails()) {
            $msg = [
                'msg' => 'Debe proporcionar al menos un campo',
                'status' => 'failed',
                'code' => '400',
            ];
            return response()->json($msg);
        }

        if ($id) {
            $host = Host::find($id);
        }else{
            $host = $request->user;
        }
        
        if (!$host) {
            $msg = [
                'msg' => 'No se encontró al host',
                'status' => 'failed',
                'code' => '400',
            ];
            return response()->json($msg);
        }
    
        if ($request->filled('password')) {
            $newPassword = $request->input('password');
            $oldPassword = $host->password;
    
            if (Hash::check($newPassword, $oldPassword)) {
                $msg = [
                    'msg' => 'La contraseña no ha cambiado ya que es idéntica a la anterior',
                    'status' => 'failed',
                    'code' => '400',
                ];
                return response()->json($msg);
            }
    
            $host->password = Hash::make($newPassword);
        }
    
        if ($request->filled('username')) {
            $newUsername = $request->input('username');
            if ($newUsername === $host->userName) {
                $msg = [
                    'msg' => 'El username no ha cambiado ya que es idéntico al anterior',
                    'status' => 'failed',
                    'code' => '400',
                ];
                return response()->json($msg);
            }
            $host->userName = $newUsername;
        }
    
        if ($request->filled('email')) {
            $newEmail = $request->input('email');
            if ($newEmail === $host->email) {
                $msg = [
                    'msg' => 'El email no ha cambiado ya que es idéntico al anterior',
                    'status' => 'failed',
                    'code' => '400',
                ];
                return response()->json($msg);
            }
            $host->email = $newEmail;
        }
    
        $host->save();
    
        $msg = [
            'msg' => 'Datos actualizados correctamente',
            'status' => 'success',
            'code' => '200',
            'data' => $host
        ];
        return response()->json($msg);
            
    }
    
    protected function delete(Request $request, $id=null)
    {
        if ($id) {
            $host = Host::find($id);
        }else{
            $host = $request->user;
        }
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

    protected function getBookings(Request $request, $id=null){
        
        if($id){
            $host = Host::findOrFail($id);
        }else{
            $host = Host::findOrFail($request->user->id);
        }

        $bookings = $host->bookings;
        $msg = [
            'msg' => 'Reservas del host '.$host->id,
            'status' => 'success',
            'code' => '201',
            'data' => $bookings
        ];
        return response()->json($msg);

    }

    protected function getHouses(Request $request, $id=null){
        if($id){
            $host = Host::findOrFail($id);
        }else{
            $host = Host::findOrFail($request->user->id);
        }
        $houses = $host->houses;
        $msg = [
            'msg' => 'Casas del host '.$host->id,
            'status' => 'success',
            'code' => '201',
            'data' => $houses
        ];
        return response()->json($msg);

    }
}

