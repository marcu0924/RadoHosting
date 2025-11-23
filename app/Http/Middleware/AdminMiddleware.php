<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Must be logged in
        if (! $user) {
            return redirect()->route('login');
        }

        // Your role column is a string: 'user' or 'admin'
        if ($user->role !== 'admin') {
            // You can change this to redirect('/') if you prefer
            abort(403, 'You do not have permission to access the admin panel.');
        }

        return $next($request);
    }
}
