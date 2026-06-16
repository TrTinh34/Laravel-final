<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // Mã đơn hàng VD: ORD-20240001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Thông tin khách hàng (lưu riêng để không mất khi user bị xóa)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            
            // Trạng thái đơn hàng
            $table->enum('status', [
                'pending',      // Chờ xác nhận
                'processing',   // Đang xử lý
                'shipping',     // Đang giao hàng
                'completed',    // Hoàn thành
                'cancelled'     // Đã hủy
            ])->default('pending');
            
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};