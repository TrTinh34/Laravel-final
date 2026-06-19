<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Giữ nguyên Đăng ký alias cho RoleMiddleware của bạn
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // 2. Cấu hình loại trừ kiểm tra CSRF Token cho VNPAY IPN (Webhook)
        $middleware->validateCsrfTokens(except: [
            'vnpay-ipn', // Thay thế 'payos-webhook' bằng route IPN mới của VNPAY
        ]);
    })
    
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();