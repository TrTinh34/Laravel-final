@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-black dark:text-white">Quản lý Đơn hàng</h2>
        <span class="text-sm text-gray-500">Tổng: {{ $orders->total() }} đơn hàng</span>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="mb-6 rounded-xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
        <form method="GET" action="{{ route('orders.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            {{-- Tìm kiếm --}}
            <div class="lg:col-span-2">
                <label class="mb-1 block text-sm font-medium text-black dark:text-white">Tìm kiếm</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Mã đơn, tên, email khách..."
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm outline-none focus:border-primary dark:border-strokedark dark:text-white">
            </div>

            {{-- Trạng thái --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-black dark:text-white">Trạng thái</label>
                <select name="status"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
                    <option value="">-- Tất cả --</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Từ ngày --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-black dark:text-white">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
            </div>

            {{-- Đến ngày --}}
            <div>
                <label class="mb-1 block text-sm font-medium text-black dark:text-white">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
            </div>

            {{-- Nút --}}
            <div class="flex items-end gap-2 lg:col-span-5">
                <button type="submit"
                    style="background-color:#3C50E0; color:#ffffff;"
                    class="rounded-lg px-5 py-2 text-sm font-medium transition hover:opacity-90">
                    🔍 Tìm kiếm
                </button>
                <a href="{{ route('orders.index') }}"
                    class="rounded-lg border border-stroke px-5 py-2 text-sm font-medium text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4">
                    ✖ Xóa lọc
                </a>
            </div>
        </form>
    </div>

    {{-- Bảng danh sách --}}
    <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Mã đơn</th>
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Khách hàng</th>
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Tổng tiền</th>
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Trạng thái</th>
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Ngày tạo</th>
                        <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr class="border-t border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                        <td class="px-4 py-4 text-sm font-medium text-black dark:text-white">
                            {{ $order->order_code }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-sm font-medium text-black dark:text-white">{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_email }}</div>
                        </td>
                        <td class="px-4 py-4 text-sm text-black dark:text-white">
                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                        </td>
                        <td class="px-4 py-4">
                            @php
                                $colorMap = [
                                    'pending'    => 'bg-yellow-100 text-yellow-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'shipping'   => 'bg-indigo-100 text-indigo-800',
                                    'completed'  => 'bg-green-100 text-green-800',
                                    'cancelled'  => 'bg-red-100 text-red-800',
                                ];
                                $colorClass = $colorMap[$order->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-block rounded-full px-3 py-1 text-xs font-semibold {{ $colorClass }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-4">
                            <a href="{{ route('orders.show', $order) }}"
                                style="background-color:#3C50E0; color:#ffffff;"
                                class="inline-block rounded-lg px-3 py-1.5 text-xs font-medium transition hover:opacity-90">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                            Không có đơn hàng nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if($orders->hasPages())
        <div class="border-t border-stroke px-4 py-4 dark:border-strokedark">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection