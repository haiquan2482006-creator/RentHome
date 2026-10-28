<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CheckLockedAccount::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'qlvan_hanh/update_payment_status',
            'api/moderator/*',
            'api/kiem-duyet/*',
            'api/cskh/*',
            'admin/posts/*/toggle-featured',
            'Admin/posts/*/toggle-featured',
            'admin/tickets/*',
            'Admin/tickets/*',
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\RestrictModeratorToManagement::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
                ], 419);
            }

            return redirect()->route('login')
                ->withInput($request->except('password', '_token'))
                ->withErrors(['login_error' => 'Phiên làm việc đã hết hạn hoặc mã bảo mật không khớp. Vui lòng thử đăng nhập lại!']);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.',
                    ], 419);
                }

                return redirect()->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->withErrors(['login_error' => 'Phiên làm việc đã hết hạn hoặc mã bảo mật không khớp. Vui lòng thử đăng nhập lại!']);
            }
        });
    })->create();
