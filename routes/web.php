<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
// Trang chủ bên ngoài (mọi người đều xem được)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =========================================================================
// KHU VỰC ĐÃ ĐĂNG NHẬP (Yêu cầu phải login - auth)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Trang Dashboard chung sau khi đăng nhập
    Route::get('/dashboard', function () {
        return view('dashboard', ['title' => 'Dashboard']);
    })->name('dashboard');

    // ---------------------------------------------------------------------
    // PHÂN NHÓM 1: CHỈ ADMIN MỚI VÀO ĐƯỢC (Quản lý User)
    // ---------------------------------------------------------------------
    Route::middleware(['role:admin'])->group(function () {
        // Đưa Resource users của bạn vào đây để bảo mật, chặn Editor và Customer
        Route::resource('users', UserController::class);
    });

    // ---------------------------------------------------------------------
    // PHÂN NHÓM 2: CẢ ADMIN & EDITOR ĐỀU VÀO ĐƯỢC (Danh mục, Sản phẩm, Đơn hàng)
    // ---------------------------------------------------------------------
    Route::middleware(['role:editor'])->group(function () {

        // [Tính năng 1]: Quản lý danh mục sản phẩm (Sẽ làm ở bước tiếp theo)
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // [Tính năng 2]: Quản lý sản phẩm (Thêm, xóa, sửa, kèm hình ảnh)
        Route::resource('products', ProductController::class);

        // [Tính năng 3]: Quản lý đơn hàng (Xem danh sách, chi tiết, lọc, tìm kiếm...)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

require __DIR__ . '/auth.php';
