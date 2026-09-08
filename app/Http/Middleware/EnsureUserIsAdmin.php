<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        if (! $user || ! $user->roles()->where('name', 'ADMIN')->exists()) {
            abort(403, 'This action requires admin access.');
        }

        return $next($request);
    }
}
