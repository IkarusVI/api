<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route('id');

        if (!is_numeric($id) || intval($id) != $id) {
            $msg = [
                'msg' => 'La ID debe ser numérica y entera.',
                'status' => '400',
            ];
            return response()->json($msg);
        }

        return $next($request);
    }
}
