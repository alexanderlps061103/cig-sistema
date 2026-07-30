<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * $roles acepta formato "rolA|rolB" (OR).
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $persona = $user->persona;
        $assigned = collect();

        if ($persona && method_exists($persona, 'roles')) {
            $assigned = $persona->roles->pluck('nombre')->map(fn($r) => strtolower($r));
        }

        $required = collect(explode('|', strtolower($roles)));

        if ($assigned->intersect($required)->isEmpty()) {
            abort(403, 'No autorizado.');
        }

        return $next($request);
    }
}
