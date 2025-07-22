<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles)
    {
    $user = Auth::user();

    // convert roles string to array
    $roleArray = explode(',', $roles);

    if (!$user || !in_array($user->role->name, $roleArray)) {
        abort(403, 'You do not have access.');
    }

    return $next($request);
    }
}

