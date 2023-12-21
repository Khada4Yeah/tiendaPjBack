<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Helpers\JwtAuth;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // COMPROBAR SI EL USUARIO ESTA IDENTIFICADO
        $token = $request->header("Authorization");
        $jwtAuth = new JwtAuth();
        $check_token = $jwtAuth->checkToken($token);

        if ($check_token) {
            return $next($request);
        } else {
            return response()->json(
                [
                    "message" => "El usuario no esta autenticado.",
                ],
                401
            );
        }
    }
}
