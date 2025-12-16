<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->guard('admin')->check()){
           abort(403, 'Admin access only.');
        }

        if(auth()->guard('admin')->user()->role !== 'admin'){
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        return $next($request);
    }
}
