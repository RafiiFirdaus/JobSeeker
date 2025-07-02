<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Society;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get token from request parameter or Authorization header
        $token = $request->input('token') ?? $request->header('Authorization');

        // Remove 'Bearer ' prefix if present
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        if (!$token) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        // Find society by token
        $society = Society::where('auth_token', $token)->first();

        if (!$society) {
            return response()->json([
                'message' => 'Invalid token'
            ], 401);
        }

        // Add society to request for use in controllers
        $request->merge(['authenticated_society' => $society]);

        return $next($request);
    }
}
