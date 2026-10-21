<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePinAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow PIN login and verify routes
        if ($request->routeIs('pin.login') || $request->routeIs('pin.verify')) {
            return $next($request);
        }

        // Check if user has authenticated with PIN
        if (!session('pin_authenticated', false)) {
            return redirect()->route('pin.login');
        }

        return $next($request);
    }
}
