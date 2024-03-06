<?php

namespace App\Http\Controllers;
use App\Models\House;
use App\Models\Booking;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Host;
use DateTime;

class BookingController extends Controller
{   
    protected function getAll(Request $request){
        $booking = Booking::all();

        $msg= [
            'msg' => 'Listado cargado con éxito',
            'status' => '200',
            'data' => $booking
        ];

        return response()->json($msg);
    }
    
    protected function getOne(Request $request, $id){
        $booking = Booking::where('id',$id)->first();
        if (!$booking) {
            $msg = [
                'msg' => 'Reserva no encontrada',
                'status' => '400',
            ];
            return response()->json($msg);
        }
        $msg = [
            'msg' => 'Reserva encontrada',
            'status' => '200',
            'data' => $booking
        ];

        return response()->json($msg); 
    }

    protected function create(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'checkIn'   =>   'required',
            'checkOut'  =>   'required',
            'arrival'   =>   'required',
            'guestN'    =>   'required'
        ]);

        if ($validator->fails()) {
            $msg = [
                'msg' => 'Uno o más campos vacios',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $checkIn = $request->checkIn;
        $checkOut = $request->checkOut;
        
        if ($checkIn >= $checkOut) {
            $msg = [
                'msg' => 'Fechas de check-in y check-out no válidas',
                'status' => '400',
            ];
            return response()->json($msg);
        }
    
        $existingBookings = Booking::where('houseId', $id)
        ->where(function($query) use ($checkIn, $checkOut) {
            $query->where('checkIn', '<', $checkOut)
                ->where('checkOut', '>', $checkIn);
        })
        ->orWhere(function($query) use ($checkIn, $checkOut) {
            $query->where('checkIn', '>=', $checkIn)
                ->where('checkOut', '<=', $checkOut);
        })
        ->orWhere(function($query) use ($checkIn, $checkOut) {
            $query->where('checkIn', '<=', $checkIn)
                ->where('checkOut', '>=', $checkOut);
        })
        ->get();

        if ($existingBookings->isNotEmpty()) {
            $msg = [
                'msg' => 'La casa ya está reservada en esas fechas',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $house = House::find($id);

        $guestId = $request->user->id;
        $houseId= $id;
        $hostId = $house->host_id;

        if ($house->maxGuests < $request->guestN) {
            $msg = [
                'msg' => 'Número máximo de huespedes alcanzado',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $checkInDate = new DateTime($request->checkIn);
        $checkOutDate = new DateTime($request->checkOut);

        $nNights = $checkInDate->diff($checkOutDate)->days;

        $booking = new Booking();
        $booking->houseId = $houseId;
        $booking->guestId = $request->user->id;
        $booking->hostId = $hostId;
        $booking->checkIn = $request->checkIn;
        $booking->checkOut = $request->checkOut;
        $booking->arrival = $request->arrival;
        $booking->price = $nNights * $house->price;
        $booking->guestN = $request->guestN;
    
        $booking->save();

        $moneyForHost = $booking->price * 0.95;

        $host = Host::find($hostId);
        $host->balance += $moneyForHost;
        $host->save();
        
        $msg = [
            'msg' => 'Reserva creada correctamente',
            'status' => '200',
            'data'=> $booking
        ];
        return response()->json($msg);
        
    }
    
    protected function delete(Request $request){
        $booking=Booking::where('id',$id)->first();

        if (!$booking) {
            $msg = [
                'msg' => 'Reserva no encontrada',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        $booking->delete();
        $msg = [
            'msg' => 'Reserva eliminada correctamente',
            'status' => '200',
            'data' => $booking
        ];

        return response()->json($msg);
    }
}
