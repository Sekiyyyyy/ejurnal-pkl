<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan rolenya adalah super_admin
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return $next($request);
        }

        // Jika bukan admin, tendang kembali ke halaman utama / login
        return redirect('/')->withErrors(['access' => 'Akses ditolak. Halaman ini khusus Super Admin.']);
    }
}