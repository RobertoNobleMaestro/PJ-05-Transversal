<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$allowedRoles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $rolesMap = [
            'admin' => 1,
            'cliente' => 2,
            'gestor' => 3,
            'mecanico' => 4,
            'admin_financiero' => 5,
            'chofer' => 6,
        ];

        $userRoleId = $request->user()->id_roles;

        // Convertir roles permitidos a IDs
        $allowedRoleIds = collect($allowedRoles)
            ->map(fn($role) => $rolesMap[$role] ?? null)
            ->filter()
            ->toArray();

        // Reglas jerárquicas: admin > gestor/admin_financiero > mecanico > cliente
        $accessRules = [
            1 => ['admin', 'gestor', 'cliente', 'mecanico', 'admin_financiero'],   // admin puede ver todo
            3 => ['gestor', 'cliente'],                                    // gestor solo gestor y cliente
            2 => ['cliente'],                                              // cliente solo su parte
            4 => ['mecanico'],                                             // mecánico solo su parte
            5 => ['admin_financiero'],                                     // admin financiero solo su parte
            6 => ['chofer']
        ];

        // Obtener los roles que puede acceder este usuario
        $userAccessRoles = $accessRules[$userRoleId] ?? [];

        // Si alguno de los roles solicitados está dentro de los accesibles por el usuario, permitir
        $canAccess = !empty(array_intersect($allowedRoles, $userAccessRoles));

        if (!$canAccess) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección.'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}

