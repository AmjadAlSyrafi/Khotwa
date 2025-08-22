<?php

namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminAuditLogMiddleware
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = $request->user();
        if (!$user || optional($user->role)->name !== 'Admin') {
            return $response;
        }

        // Determine action from HTTP verb
        $method = strtolower($request->method());
        $action = match ($method) {
            'post'   => 'create',
            'put', 'patch' => 'update',
            'delete' => 'delete',
            default  => 'view',
        };

        // Try to guess entity_type from route/controller
        $routeName = $request->route()?->getName();
        $entityType = $routeName ? Str::before($routeName, '.') : 'Unknown';

        // Grab ID if present in route params
        $entityId = $request->route('id') ?? null;

        $this->auditLogService->log($action, ucfirst($entityType), $entityId);

        return $response;
    }
}
