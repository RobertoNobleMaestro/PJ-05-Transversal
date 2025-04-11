<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $roleId = $role === 'admin' ? 1 : 2;
        if ($request->user()->id_roles !== $roleId) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
