<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\House;


class FavoriteController extends Controller
{
    protected function getAll(Request $request){
        $favorite = Favorite::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => '200',
            'data' => $favorite
        ];
        return response()->json($msg);
    }
    protected function create(Request $request){
        $guest = $request->user;

        $guestId = $guest->id;
        $houseId = $request->id;

        $house = House::find($houseId);

        if (!$house) {
            $msg = [
                'msg' => 'La casa con la ID proporcionada no existe',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $existingFavorite = Favorite::where('ownerId', $guestId)
                                    ->where('houseId', $houseId)
                                    ->first();

        if ($existingFavorite) {
            $msg = [
                'msg' => 'Ya tienes esta casa agregada a favoritos',
                'status' => '400',
            ];
            return response()->json($msg);
        }


        $favorite = new Favorite();
        $favorite->ownerId = $guestId;
        $favorite->houseId = $houseId;
        $favorite->save();

        $msg = [
            'msg' => 'Casa agregada a favoritos',
            'status' => '200',
        ];
        return response()->json($msg);

    }
    protected function delete(Request $request, $id){
        $ownerId   = $request->user->id;
        $houseId = $request->id; 
        
        $favorite = Favorite::where('ownerId', $ownerId)
                        ->where('houseId', $houseId)
                        ->first();
    
        if(!$favorite){
            $msg = [
                'msg' => 'Favorito no encontrado',
                'code' => '400',
            ];
            return response()->json($msg);
        }
    
        if($favorite->ownerId != $request->user->id){
            $msg = [
                'msg' => 'No puedes borrar un favorito que no es tuyo',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        
        $favorite->delete();
        $msg = [
            'msg' => 'Favorito eliminado correctamente',
            'status' => '200',
            'data' => $favorite
        ];
        return response()->json($msg);
    }
    
}
