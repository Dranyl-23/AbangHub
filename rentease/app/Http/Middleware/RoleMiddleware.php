<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user's role is in the allowed roles array
        if (!in_array($user->user_type, $roles)) {
            // If they don't have permission, redirect to their respective dashboard or 403
            return abort(403, 'Unauthorized access. You do not have the required role to view this page.');
        }

        return $next($request);
    }
}
