<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Đếm số tài khoản có role là customer
        $totalCustomers = User::where('role', 'customer')->count();

        // 2. Đếm tổng số đơn hàng trong database
        $totalOrders = Order::count();

        // 3. Tính tổng doanh thu từ các đơn hàng đã hoàn thành (status = 'completed')
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');

        // Truyền các biến này sang View
        return view('dashboard', compact('totalCustomers', 'totalOrders', 'totalRevenue'));
    }
}
