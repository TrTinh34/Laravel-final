<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // DỌN DẸP RÁC: Xóa sạch dữ liệu cũ của bảng đơn hàng trước khi nạp mới
        // =========================================================================
        // Phải xóa bảng chi tiết đơn hàng (order_items) trước để tránh lỗi ràng buộc khóa ngoại
        DB::table('order_items')->delete();
        DB::table('orders')->delete();

        // =========================================================================
        // SINH DỮ LIỆU GIẢ LẬP MỚI (Bắt đầu lại từ số thứ tự 1)
        // =========================================================================
        $statuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
        $products = Product::all();

        for ($i = 1; $i <= 30; $i++) {
            $order = Order::create([
                'order_code'       => 'ORD-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'customer_name'    => 'Khách hàng ' . $i,
                'customer_email'   => 'customer' . $i . '@example.com',
                'customer_phone'   => '09' . rand(10000000, 99999999),
                'customer_address' => rand(1, 100) . ' Đường Lê Lợi, Q.' . rand(1, 12) . ', TP.HCM',
                'status'           => 'pending',
                'total_amount'     => 0,
                'created_at'       => now()->subDays(rand(0, 60)),
            ]);

            $total = 0;
            $itemCount = rand(1, 4);

            for ($j = 0; $j < $itemCount; $j++) {
                if ($products->isNotEmpty()) {
                    $product  = $products->random();
                    $price    = $product->price ?? rand(100000, 2000000);
                    $qty      = rand(1, 3);
                    $subtotal = $price * $qty;
                    $total   += $subtotal;

                    OrderItem::create([
                        'order_id'      => $order->id,
                        'product_id'    => $product->id,
                        'product_name'  => $product->name,
                        'product_price' => $price,
                        'quantity'      => $qty,
                        'subtotal'      => $subtotal,
                    ]);
                }
            }

            // Cập nhật lại tổng tiền chính xác sau khi đã tính tổng các item
            $order->update(['total_amount' => $total]);
        }
    }
}