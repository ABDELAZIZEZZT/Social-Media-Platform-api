<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class verifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = $request->bearerToken();
            if($token){
                $user=JWTAuth::parseToken()->Authenticate();
                return $next($request);
            }else{
                return response()->json(
                    ['messag' => 'token is requaired']
                );
            }

        } catch (JWTException $e) {
           if($e instanceof TokenInvalidException){
            return response()->json(
                ['messag' => 'token is invalid']
            );
           }else if($e instanceof TokenExpiredException){
            return response()->json(
                ['messag' => 'token is expaired']
            );
           }else{
            return response()->json(
                ['messag' => 'exaption']
            );
           }
        }
    }
}
