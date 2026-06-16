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
        // 1. Kiểm tra xem user đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Nếu là Admin thì cho phép đi tiếp vào TẤT CẢ các trang quản trị
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 3. Kiểm tra xem role của user hiện tại có nằm trong danh sách các quyền được phép không
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Nếu không có quyền, hiển thị trang lỗi 403 (Không có quyền truy cập)
        abort(403, 'Bạn không có quyền truy cập vào trang này.');
    }
}
