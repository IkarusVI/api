<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Guest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\House;
use App\Http\Controllers\HouseController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class GuestController extends Controller
{
     
    protected function getAll(Request $request)
    {
        $guest = Guest::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => '200',
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
                'status' => '400'
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Guest Encontrado',
            'status' => '200',
            'data' => $guest
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
                    'status' => '200',
                    'data' => $guest
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

            'msg' => 'Este guest ya existe',
            'status' => '400',
        ];
        return response()->json($msg);
     
    }
    protected function modify(Request $request, $id=null)
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
        if ($id) {
            $guest = Guest::find($id);
        }else{
            $guest = $request->user;
        }
        if (!$guest) {
            $msg = [
                'msg' => 'No se encontró al guest',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        
        if ($request->filled('password')) {
            $newPassword = $request->input('password');
            $oldPassword = $guest->password;
    
            if (Hash::check($newPassword, $oldPassword)) {
                $msg = [
                    'msg' => 'La contraseña no ha cambiado ya que es idéntica a la anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
    
            $guest->password = Hash::make($newPassword);
        }
    
        if ($request->filled('userName')) {
            $newUsername = $request->input('userName');
            if ($newUsername === $guest->userName) {
                $msg = [
                    'msg' => 'El username no ha cambiado ya que es idéntico al anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
            $guest->userName = $newUsername;
        }
    
        if ($request->filled('email')) {
            $newEmail = $request->input('email');
            if ($newEmail === $guest->email) {
                $msg = [
                    'msg' => 'El email no ha cambiado ya que es idéntico al anterior',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
            $guest->email = $newEmail;
        }
    
        $guest->save();
    
        $msg = [
            'msg' => 'Datos actualizados correctamente',
            'status' => '400',
            'data' => $guest
        ];
        return response()->json($msg);
    }
    protected function delete(Request $request, $id=null)
    {
        if($id){
            $guest = Guest::findOrFail($id);
        }else{
            $guest = Guest::findOrFail($request->user->id);
        }
        if (!$guest){
            $msg = [
                'msg' => 'Host no encontrado',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        $guest->delete();
        $msg = [
            'msg' => 'Guest eliminado correctamente',
            'status' => '200',
            'data' => $guest
        ];

        return response()->json($msg);

    }
    protected function getBookings(Request $request, $id=null){
        
        if($id){
            $guest = Guest::findOrFail($id);
        }else{
            $guest = Guest::findOrFail($request->user->id);
        }

        $bookings = $guest->bookings;
        $msg = [
            'msg' => 'Reservas del host '.$guest->id,
            'status' => '200',
            'data' => $bookings
        ];
        return response()->json($msg);

    }
    
    protected function getFavorites(Request $request, $id=null){
        
        $userId = $id ? $id : $request->user->id;
        $guest = Guest::findOrFail($userId);
        $favorites = $guest->favorites;
        $houses = [];

        foreach ($favorites as $favorite) {
            $id = $favorite->houseId;
            $house = House::where('id',$id)->first();
    
            $houses[] = $house;
        }

        $msg = [
            'msg' => 'Casas favoritas del usuario ' . $guest->userName,
            'status' => '200',
            'data' => $houses
        ];
    
        return response()->json($msg);
    }
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
        }else{
            Auth::login($existingGuest);
            $token = $existingGuest->createToken("token")->plainTextToken;
            
            $msg = [
                'msg' => 'Bienvenido',
                'status' => '200',
                'data'=> $token
            ];
            return $msg;
        }

    }    
    
}
