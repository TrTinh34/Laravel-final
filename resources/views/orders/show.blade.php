@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-xl p-4 md:p-6 2xl:p-10">

    {{-- Header --}}
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('orders.index') }}"
            class="flex items-center gap-1 text-sm text-gray-500 hover:text-primary dark:text-gray-400">
            ← Quay lại
        </a>
        <h2 class="text-2xl font-bold text-black dark:text-white">
            Chi tiết Đơn hàng: {{ $order->order_code }}
        </h2>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Cột trái --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Sản phẩm trong đơn --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-lg font-semibold text-black dark:text-white">Sản phẩm đã đặt</h3>
                </div>
                <div class="p-6">
                    @if($order->orderItems->count() > 0)
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-stroke dark:border-strokedark text-left">
                                <th class="pb-3 font-semibold text-black dark:text-white">Sản phẩm</th>
                                <th class="pb-3 font-semibold text-black dark:text-white text-right">Đơn giá</th>
                                <th class="pb-3 font-semibold text-black dark:text-white text-center">SL</th>
                                <th class="pb-3 font-semibold text-black dark:text-white text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr class="border-b border-stroke dark:border-strokedark">
                                <td class="py-3 text-black dark:text-white">{{ $item->product_name }}</td>
                                <td class="py-3 text-right text-gray-600 dark:text-gray-400">
                                    {{ number_format($item->product_price, 0, ',', '.') }}đ
                                </td>
                                <td class="py-3 text-center text-gray-600 dark:text-gray-400">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3 text-right font-medium text-black dark:text-white">
                                    {{ number_format($item->subtotal, 0, ',', '.') }}đ
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="pt-4 text-right font-bold text-black dark:text-white">
                                    Tổng cộng:
                                </td>
                                <td class="pt-4 text-right text-lg font-bold" style="color:#3C50E0">
                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    @else
                    <p class="text-sm text-gray-500">Không có sản phẩm nào trong đơn hàng này.</p>
                    @endif
                </div>
            </div>

            {{-- Thông tin khách hàng --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-lg font-semibold text-black dark:text-white">Thông tin khách hàng</h3>
                </div>
                <div class="grid grid-cols-1 gap-4 p-6 sm:grid-cols-2">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Họ tên</p>
                        <p class="mt-1 font-medium text-black dark:text-white">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                        <p class="mt-1 font-medium text-black dark:text-white">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Số điện thoại</p>
                        <p class="mt-1 font-medium text-black dark:text-white">{{ $order->customer_phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Địa chỉ</p>
                        <p class="mt-1 font-medium text-black dark:text-white">{{ $order->customer_address ?? '—' }}</p>
                    </div>
                    @if($order->notes)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Ghi chú</p>
                        <p class="mt-1 font-medium text-black dark:text-white">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Cột phải: Trạng thái --}}
        <div class="space-y-6">

            {{-- Trạng thái hiện tại --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-lg font-semibold text-black dark:text-white">Trạng thái đơn hàng</h3>
                </div>
                <div class="p-6">
                    @php
                    $colorMap = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'processing' => 'bg-blue-100 text-blue-800',
                    'shipping' => 'bg-indigo-100 text-indigo-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    ];
                    $colorClass = $colorMap[$order->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-block rounded-full px-4 py-2 text-sm font-semibold {{ $colorClass }}">
                        {{ $order->status_label }}
                    </span>
                    <div class="mt-4 space-y-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Ngày tạo: {{ $order->created_at->format('d/m/Y H:i') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Cập nhật: {{ $order->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            @if($order->status === 'pending')
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-lg font-semibold text-black dark:text-white">Thanh toán đơn hàng</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('payment.checkout', $order->id) }}"
                        class="flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:opacity-90">
                        Thanh toán VNPay
                    </a>
                </div>
            </div>
            @endif

            {{-- Form cập nhật trạng thái --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                    <h3 class="text-lg font-semibold text-black dark:text-white">Cập nhật trạng thái</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status"
                            class="mb-4 w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                                   outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
                            @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        <button type="submit"
                            style="background-color:#3C50E0;color:#ffffff;"
                            class="w-full rounded-lg px-4 py-2 text-sm font-medium transition hover:opacity-90">
                            Cập nhật trạng thái
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection