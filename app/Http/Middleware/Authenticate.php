<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API calls, just return null so it returns 401 Unauthorized JSON response
        if ($request->expectsJson()) {
            return null;
        }

        // If you do have a web login route, replace with that route name, otherwise just null
        // return route('login');
        return null;
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // For web requests, fallback to default redirect (can be customized)
        return redirect()->guest($this->redirectTo($request) ?? '/login');
    }
}
