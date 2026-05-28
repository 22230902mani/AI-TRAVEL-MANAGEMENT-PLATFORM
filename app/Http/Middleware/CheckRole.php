<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            abort(403, 'Unauthorized');
        }
        
        $roles = explode('|', $role);
        
        if (! $request->user()->hasRole($roles)) {
            abort(403, 'Forbidden. Required roles: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
