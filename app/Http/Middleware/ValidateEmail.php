<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        return $next($request);
        */
        $email = $request->email;

        if (!is_numeric($email) && strpos($email, '@') !== false && (str_ends_with($email, '.es') || str_ends_with($email, '.com'))) {
            return $next($request);
        }

        $msg= [
            'msg' => 'Email no es válido',
            'status' => 'failed',
            'code' => '400'
        ];

        return response()->json($msg);
        
    }

}
