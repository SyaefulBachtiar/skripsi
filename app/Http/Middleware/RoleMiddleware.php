<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array(Auth::check() ? Auth::user()->role : null, $roles)) {
            // Jangan redirect ke login lagi! Berikan 403 Forbidden
            return redirect()->route('beranda')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }
        return $next($request);
    }
}
