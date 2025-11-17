<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;

// --- 1. TAMBAHKAN IMPOR INI DI ATAS ---
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // --- 2. TAMBAHKAN BLOK INI ---
        // Menambahkan middleware CORS ke grup 'api'
        // Ini akan menangani Preflight (OPTIONS) request
        $middleware->appendToGroup('api', [
            HandleCors::class,
        ]);
        // --- AKHIR BLOK TAMBAHAN ---

        $middleware->alias([
            'role' => CheckRole::class,
            // ... (sisa alias Anda)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();