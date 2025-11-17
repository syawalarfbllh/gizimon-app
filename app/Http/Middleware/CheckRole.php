<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  ...string  $roles (Kita bisa memasukkan satu atau lebih peran, misal: 'admin', 'penyedia')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // 2. Ambil user dan nama perannya (via relasi 'role' yang kita buat di Model)
        $user = Auth::user();
        $userRole = $user->role->nama_role;

        // 3. Cek apakah peran user ada di dalam daftar $roles yang diizinkan
        foreach ($roles as $role) {
            if ($userRole == $role) {
                // 4. Jika diizinkan, lanjutkan request
                return $next($request);
            }
        }

        // 5. Jika tidak diizinkan, kirim pesan error 'Forbidden'
        return response()->json(['message' => 'Forbidden. Anda tidak memiliki akses.'], 403);
    }
}