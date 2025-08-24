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

        if (!$user || !$user->role) {
            abort(403, 'You do not have access.');
        }

        // convert roles string to array
        $roleArray = explode(',', $roles);
        $userRole  = $user->role->name;

        // special case: Supervisor also has Volunteer permissions
        if ($userRole === 'Supervisor' && in_array('Volunteer', $roleArray)) {
            return $next($request);
        }

        // check normal role access
        if (!in_array($userRole, $roleArray)) {
            abort(403, 'You do not have access.');
        }

        return $next($request);
    }
}
