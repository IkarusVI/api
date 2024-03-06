<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\House;
use Illuminate\Support\Facades\Validator;


use Illuminate\Http\Request;

class HouseController extends Controller
{
    protected function getAll(Request $request){
        $house = House::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => '200',
            'data' => $house
        ];

        return response()->json($msg);
    }
    protected function getOne(Request $request, $id){
        $house = House::where('id',$id)->first();
        if (!$house) {
            $msg = [
                'msg' => 'Casa no encontrada',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Casa Encontrado',
            'status' => '200',
            'data' => $house
        ];

        return response()->json($msg); 
    }
    protected function create(Request $request)
    {
        $hostId = $request->user->id;

        //return response($hostId);
        
        $validator = Validator::make($request->all(), [
            'image' =>      'required',
            'name' =>       'required',
            'location' =>   'required',
            'description'=> 'required',
            'price'=>       'required',
            'maxGuests'=>   'required'
        ]);

        if ($validator->fails()) {
            $msg = [
                'msg' => 'Uno o más campos vacíos',
                'status' => '400',
                'errors' => $validator->errors()
            ];
            return response()->json($msg);
        }

        $house = new House();
        $house->image = $request->image;
        $house->host_id = $hostId;
        $house->name = $request->name;
        $house->location = $request->location;
        $house->description = $request->description;
        $house->price = $request->price;
        $house->maxGuests = $request->maxGuests;
        $house->save();

        $msg = [
            'msg' => 'Casa creada correctamente',
            'status' => '200',
            'data' => $house
        ];
        return response()->json($msg);
    }
    protected function modify(Request $request, $id){

        if($request->class=='Guest'){
            $msg = [
                'msg' => 'Acceso no permitido',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $validator = Validator::make($request->all(), [
            'image'         =>   'required_without_all:name,location,description,price,maxGuests',
            'name'          =>   'required_without_all:image,location,description,price,maxGuests',
            'location'      =>   'required_without_all:image,name,description,price,maxGuests',
            'description'   =>   'required_without_all:image,name,location,price,maxGuests',
            'price'         =>   'required_without_all:image,name,location,description,maxGuests',
            'maxGuests'     =>   'required_without_all:image,name,location,description,price',
        ]);

        if ($validator->fails()) {
            $msg = [
                'msg' => 'Debe proporcionar al menos un campo',
                'status' => '400',
            ];
            return response()->json($msg);
        }   
        
        $house = House::find($id);
        if($request->class=='Host'){
            if($house->host_id != $request->user->id){
                $msg = [
                    'msg' => 'No puedes modificar una casa que no es tuya',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
        }

        if (!$house) {
            $msg = [
                'msg' => 'La casa no fue encontrada',
                'status' => '400',
            ];
            return response()->json($msg);
        }    
    
        $attributes = [
            'name' => 'Nombre',
            'location' => 'Localización',
            'description' => 'Descripción',
            'price' => 'Precio',
            'maxGuests' => 'Máximo de ocupantes',
            'image'=> 'Imagen',
        ];
        
        foreach ($attributes as $attributeKey => $attributeName) {
            if ($request->filled($attributeKey)) {
                $newValue = $request->input($attributeKey);
                if ($newValue === $house->$attributeKey) {
                    $msg = [
                        'msg' => "El atributo: $attributeName no ha cambiado ya que es idéntico al anterior",
                        'status' => '400',
                    ];
                    return response()->json($msg);
                }
                $house->$attributeKey = $newValue;
            }
        }
        
        $house->save();
    
        $msg = [
            'msg' => 'Datos actualizados correctamente',
            'status' => '200',
            'data' => $house
        ];
        return response()->json($msg);

    


    }
    protected function delete(Request $request, $id){


        if($request->class=='Guest'){
            $msg = [
                'msg' => 'Acceso no permitido',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        
        $house = House::find($id);

        if (!$house) {
            $msg = [
                'msg' => 'Casa no encontrada',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        if($request->class=='Host'){
            if($house->host_id != $request->user->id){
                $msg = [
                    'msg' => 'No puedes borrar una casa que no es tuya',
                    'status' => '400',
                ];
                return response()->json($msg);
            }
        }
        $imagePath = null;
        if (strpos($house->image, 'http://127.0.0.1:8000/storage/') === 0) {
            $imageName = str_replace('http://127.0.0.1:8000/storage/', '', $house->image);
            $imagePath = storage_path('app/public/' . $imageName);
        }

        $house->bookings()->delete();
        $house->delete();
    
        if ($imagePath !== null && file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $house->bookings()->delete();
        $house->delete();
    
        $msg = [
            'msg' => 'Casa eliminada correctamente',
            'status' => '200',
            'data' => $house
        ];

           return response()->json($msg);
    }
    protected function storeImg(Request $request){
        $file = $request->file('img');
        $url=$file->store('public');
        $imgUrl=str_replace('public', 'storage', $url);
        $prefixUrl='http://127.0.0.1:8000/';
        $finalUrl = $prefixUrl . $imgUrl;

        $msg = [
            'msg' => 'imagen cargada correctamente',
            'status' => '200',
            'data' => $finalUrl
        ];
        return $msg;
    }
    protected function search(Request $request, $input){
        $location = $input;

        $houses = House::where('location', 'like', '%' . $location . '%')->get();
    
        if ($houses->isEmpty()) {
            $msg = [
                'msg' => 'No se encontraron casas en la ubicación especificada',
                'status' => '400',
            ];
            return response()->json($msg);
        }
    
        $msg = [
            'msg' => 'Casas encontradas',
            'status' => '200',
            'data' => $houses
        ];
        return response()->json($msg);
    }
}
