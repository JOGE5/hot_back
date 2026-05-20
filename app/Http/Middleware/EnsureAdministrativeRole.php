<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrativeRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $roles = $roles ?: ['SUPER ADMIN', 'ADMIN', 'RECEPCIONISTA', 'CHEF'];
        $user = $request->user();

        abort_if(! $user?->estado, 403);
        abort_if(! $user->role, 403);
        abort_if(! in_array($user->role->nombre, $roles, true), 403);

        return $next($request);
    }
}
