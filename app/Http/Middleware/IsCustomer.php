<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsCustomer
{
    /**
     * Memastikan hanya customer yang bisa mengakses rute ini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->isAdmin()) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Customer.');
        }

        return $next($request);
    }
}
