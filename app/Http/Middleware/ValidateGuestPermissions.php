<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Guest;
use App\Models\Host;

class ValidateGuestPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            $msg = [
                'msg' => 'Acceso Denegado, Inicia Sesión Como Guest',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $request->user = $user;
            $userClass = class_basename(($user));
            if($userClass!='Guest'){
                $msg = [
                    'msg' => 'Acceso denegado guest',
                    'status' => '400',
                ];
                return response()->json($msg); 
            }
            return $next($request);
        }
        $msg = [
            'msg' => 'tu sesión ha caducado',
            'status' => '400',
        ];

        return response()->json($msg);      
    }
}
