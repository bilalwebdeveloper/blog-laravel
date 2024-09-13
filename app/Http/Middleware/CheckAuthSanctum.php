<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAuthSanctum
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         // Check if user is authenticated via Sanctum
         if (Auth::guard('sanctum')->check()) {
            return $next($request); // If authenticated, continue with request
        }

        // If authentication fails, return custom JSON response
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Token is either invalid or missing.',
            'error' => 'Authentication required'
        ], 401);
    }
}
