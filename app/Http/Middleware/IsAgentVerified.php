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
        $agent = $request->user();

        if(!$agent){
             return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        if($agent->status !== 'approved'){
            abort(403, 'Verified Agents only.');
        }

        return $next($request);
    }
}
