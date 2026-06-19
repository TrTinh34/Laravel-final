<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderStatusChanged;
class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng — lọc, tìm kiếm, sắp xếp
     */
    public function index(Request $request)
    {
        $query = Order::with('orderItems')->latest();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo ngày (từ ngày → đến ngày)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tìm kiếm theo mã đơn hoặc tên khách
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();
        $statuses = Order::$statuses;

        return view('orders.index', compact('orders', 'statuses'));
    }

    /**
     * Chi tiết đơn hàng
     */
    public function show(Order $order)
    {
        $order->load('orderItems.product', 'user');
        $statuses = Order::$statuses;
        return view('orders.show', compact('order', 'statuses'));
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::$statuses)),
        ]);

        // 1. Cập nhật trạng thái mới vào Database
        $order->update(['status' => $request->status]);

        // 2. Kiểm tra nếu trạng thái thuộc nhóm cần gửi Email
        $targetStatuses = ['shipping', 'completed', 'cancelled']; 
        if (in_array($order->status, $targetStatuses)) {

            // Gửi Notification thẳng tới email của khách hàng lưu trong đơn
            Notification::route('mail', $order->customer_email)
                ->notify(new OrderStatusChanged($order));
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Cập nhật trạng thái đơn hàng và gửi email thông báo thành công!');
    }
}
