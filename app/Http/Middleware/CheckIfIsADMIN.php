<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfIsADMIN
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next):Response
    {
        if (auth::check() && auth::user()->is_admin) {
            return $next($request);
        }

        return response()->json(['message' => 'Accès refusé. Vous devez être un admin.'], 403);

      
    }
}
