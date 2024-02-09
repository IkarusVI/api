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
            'status' => 'success',
            'code' => '200',
            'data' => $favorite
        ];
        return response()->json($msg);
    }
    protected function create(Request $request, $id){
        $guest = $request->user;

        $guestId = $guest->id;
        $houseId = $id;

        $house = House::find($houseId);

        if (!$house) {
            $msg = [
                'msg' => 'La casa con la ID proporcionada no existe',
                'status' => 'failed',
                'code' => '404',
            ];
            return response()->json($msg);
        }

        $existingFavorite = Favorite::where('ownerId', $guestId)
                                    ->where('houseId', $houseId)
                                    ->first();

        if ($existingFavorite) {
            $msg = [
                'msg' => 'Ya tienes esta casa agregada a favoritos',
                'status' => 'failed',
                'code' => '400',
            ];
            return response()->json($msg);
        }


        $favorite = new Favorite();
        $favorite->ownerId = $guestId;
        $favorite->houseId = $houseId;
        $favorite->save();

        $msg = [
            'msg' => 'Casa agregada a favoritos',
            'status' => 'success',
            'code' => '200',
        ];
        return response()->json($msg);

    }
    protected function delete(Request $request, $id){
        $favorite=Favorite::where('id',$id)->first();
        if(!$favorite){
            $msg = [
                'msg' => 'Favorito no encontrado',
                'status' => 'failed',
                'code' => '404',
            ];
            return response()->json($msg);
        }
  
        if($favorite->ownerId != $request->user->id){
            $msg = [
                'msg' => 'No puedes borrar un favorito que no es tuyo',
                'status' => 'failed',
                'code' => '404',
            ];
            return response()->json($msg);
        }
        
        if (!$favorite) {
            $msg = [
                'msg' => 'Favorito no encontrada',
                'status' => 'failed',
                'code' => '404'
            ];
            return response()->json($msg);
        }
        $favorite->delete();
        $msg = [
            'msg' => 'Favorito eliminado correctamente',
            'status' => 'success',
            'code' => '200',
            'data' => $favorite
        ];
        return response()->json($msg);
    }
}
