<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;

// Trang chủ bên ngoài (mọi người đều xem được)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =========================================================================
// KHU VỰC ĐÃ ĐĂNG NHẬP (Yêu cầu phải login - auth)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    // Trang Dashboard chung sau khi đăng nhập
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

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

    // Chatbot
    Route::view('/chat', 'pages.chat')->name('chat.index');
    
    // Route xử lý nhận tin nhắn và gọi AI bằng AJAX
    Route::post('/chat/send', ChatController::class)->name('chat.send');

    // [PayOS]: Route kích hoạt tạo link thanh toán VietQR (Yêu cầu khách phải đăng nhập)
    Route::get('/payment/checkout/{order}', [PaymentController::class, 'createPaymentLink'])->name('payment.checkout');
});

// =========================================================================
// KHU VỰC CÔNG KHAI (Không yêu cầu đăng nhập)
// =========================================================================

// [PayOS]: Cổng nhận thông báo biến động số dư ngầm tự động (Đã loại bỏ CSRF)
// Route khởi tạo thanh toán (nhập vào orderId của bạn)
Route::get('/payment/create/{orderId}', [PaymentController::class, 'createPaymentLink'])->name('payment.create');

// Route đồng bộ hiển thị kết quả cho khách (Khớp với giá trị VNPAY_RETURN_URL trong .env)
Route::get('/vnpay-return', [PaymentController::class, 'vnpayReturn']);

// Route IPN (Nhận webhook bảo mật từ server VNPAY - Cần dùng link Expose để test)
Route::get('/vnpay-ipn', [PaymentController::class, 'handleWebhook']);

Route::get('/payment-success', function () {
    return "Thanh toán thành công! Cảm ơn bạn.";
})->name('payment.success');

Route::get('/payment-cancel', function () {
    return "Bạn đã hủy thanh toán hoặc giao dịch thất bại.";
})->name('payment.cancel');

require __DIR__ . '/auth.php';