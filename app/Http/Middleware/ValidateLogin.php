<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ValidateLogin
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
                'msg' => 'token no proporcionado',
                'status' => 'failed',
                'code'=> 401
            ];
            return response()->json($msg);
        }

        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            $request->user = $user;
            return $next($request);
        }
        $msg = [
            'msg' => 'token no valido o ha caducado',
            'status' => 'failed',
            'code'=> 401
        ];

        return response()->json($msg);      
    }
}
