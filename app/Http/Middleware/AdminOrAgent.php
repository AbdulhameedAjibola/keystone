<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrAgent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if(
            auth()->guard("admin")->check() ||
            auth()->guard("api-agent")->check()
        ){
            return $next($request);
        }

        abort(403, 'You are Unauthorized to access this resource.');
        
    }
}
