<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAgentVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
            public function handle(Request $request, Closure $next)
        {
            // Get the user from the request (resolved by previous 'auth:sanctum' middleware)
            $agent = $request->user(); 

            if (!$agent || $agent->status !== 'approved') {
                return response()->json(['message' => 'Verified Agents only.'], 403);
            }

            return $next($request);
        }
}
