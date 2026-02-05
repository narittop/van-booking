<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->isDirector() && !auth()->user()->isSuperAdmin())) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงหน้านี้ (เฉพาะผู้อำนวยการ)');
        }

        return $next($request);
    }
}
