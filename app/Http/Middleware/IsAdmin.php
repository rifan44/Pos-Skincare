<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika yang login bukan admin
        if (Auth::check() && Auth::user()->role !== 'admin') {
            return redirect()->route('pos.index')->with('error', 'Akses Ditolak! Hanya Admin yang bisa membuka menu tersebut.');
        }

        return $next($request);
    }
}