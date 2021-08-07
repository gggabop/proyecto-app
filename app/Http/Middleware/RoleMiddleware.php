<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $roles = collect(explode('|',$role));
        foreach ($roles as $role){
            if (auth()->user()->level === $role) {
                return $next($request);
            }
        }
        return response(['message'=>'No tienes autorización para ingresar.'],403);
    }
}
