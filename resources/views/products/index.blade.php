@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    {{-- HEADER TRANG --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-black dark:text-white">Quản lý Sản phẩm</h2>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Xem, thêm, sửa và phân loại sản phẩm hệ thống</p>
        </div>
        <a href="{{ route('products.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-center text-sm font-medium text-white shadow-md transition-all hover:bg-opacity-90 hover:shadow-lg">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Thêm sản phẩm
        </a>
    </div>

    {{-- THÔNG BÁO SUCCESS --}}
    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-green-50 border border-green-200 px-4 py-3.5 text-green-800 dark:bg-green-950/30 dark:border-green-900/50 dark:text-green-400 shadow-sm">
        <svg class="h-5 w-5 shrink-0 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    {{-- BỐ CỤC CHÍNH --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-4">

        {{-- SIDEBAR DANH MỤC --}}
        <div class="xl:col-span-1 space-y-4">

            {{-- Form thêm danh mục --}}
            <div class="rounded-2xl border border-stroke bg-white p-5 shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="mb-4 flex items-center gap-2 border-b border-stroke pb-2 dark:border-strokedark">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V4a2 2 0 012-2h6l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="font-semibold text-black dark:text-white">Thêm danh mục</h3>
                </div>
                <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
                    @csrf
                    <div>
                        <input type="text" name="name" placeholder="Tên danh mục..." required
                            class="w-full rounded-xl border border-stroke bg-transparent px-4 py-2 text-sm outline-none transition focus:border-primary dark:border-strokedark dark:text-white dark:focus:border-primary">
                    </div>
                    <div>
                        <textarea name="description" placeholder="Mô tả danh mục (tùy chọn)" rows="2"
                            class="w-full rounded-xl border border-stroke bg-transparent px-4 py-2 text-sm outline-none transition focus:border-primary dark:border-strokedark dark:text-white dark:focus:border-primary"></textarea>
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center rounded-xl bg-primary py-2 text-sm font-medium text-white transition hover:bg-opacity-90 shadow-sm">
                        Tạo danh mục
                    </button>
                </form>
            </div>

            {{-- Danh sách bộ lọc danh mục --}}
            <div class="rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="border-b border-stroke px-4 py-3 dark:border-strokedark bg-gray-50/50 dark:bg-meta-4/20">
                    <h3 class="font-semibold text-black dark:text-white text-sm">Danh mục sản phẩm</h3>
                </div>
                <ul class="divide-y divide-stroke dark:divide-strokedark">
                    {{-- Mục Tất cả sản phẩm --}}
                    <li>
                        <a href="{{ route('products.index') }}"
                            class="flex items-center justify-between px-4 py-3 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-meta-4
                                  {{ !$selectedCategory ? 'bg-primary/5 font-bold text-primary dark:text-white' : 'text-black dark:text-white' }}">
                            <span class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 11m8 4V4"></path>
                                </svg>
                                Tất cả sản phẩm
                            </span>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500 dark:bg-meta-4 dark:text-gray-400">
                                {{ $categories->sum('products_count') }}
                            </span>
                        </a>
                    </li>

                    {{-- Các danh mục động từ database --}}
                    @foreach($categories as $cat)
                    <li class="group relative min-w-0">
                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}"
                            class="flex items-center justify-between pl-4 pr-16 py-3 text-sm transition-colors hover:bg-gray-50 dark:hover:bg-meta-4
                                  {{ $selectedCategory == $cat->id ? 'bg-primary/5 font-bold text-primary dark:text-white' : 'text-black dark:text-white' }}">
                            <span class="truncate pr-2">{{ $cat->name }}</span>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500 dark:bg-meta-4 dark:text-gray-400 group-hover:opacity-0 transition-opacity">
                                {{ $cat->products_count }}
                            </span>
                        </a>

                        {{-- Nút sửa/xóa ẩn hiện tinh tế khi hover --}}
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 hidden items-center gap-1 group-hover:flex z-10">
                            <button onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes(preg_replace('/\s+/', ' ', $cat->description ?? '')) }}')"
                                class="p-1 rounded bg-yellow-50 text-yellow-600 border border-yellow-200 hover:bg-yellow-100 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}" onsubmit="return confirm('Xóa danh mục này?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="p-1 rounded bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- KHU VỰC BẢNG DỮ LIỆU CHÍNH --}}
        <div class="xl:col-span-3 min-w-0 space-y-4">

            {{-- Thanh bộ lọc tìm kiếm sản phẩm --}}
            <div class="rounded-2xl border border-stroke bg-white p-4 shadow-sm dark:border-strokedark dark:bg-boxdark">
                <form method="GET" action="{{ route('products.index') }}" class="flex gap-3">
                    @if($selectedCategory)
                    <input type="hidden" name="category_id" value="{{ $selectedCategory }}">
                    @endif
                    <div class="relative flex-1">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tên sản phẩm..."
                            class="w-full rounded-xl border border-stroke bg-transparent pl-10 pr-4 py-2 text-sm outline-none transition focus:border-primary dark:border-strokedark dark:text-white dark:focus:border-primary">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-2 text-sm font-medium text-white transition hover:bg-opacity-95 shadow-sm">
                        Tìm kiếm
                    </button>
                    @if(request('search'))
                    <a href="{{ route('products.index', $selectedCategory ? ['category_id' => $selectedCategory] : []) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-stroke px-4 py-2 text-sm font-medium text-black transition hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4 shadow-sm">
                        Hủy tìm
                    </a>
                    @endif
                </form>
            </div>

            {{-- Khối danh sách sản phẩm --}}
            <div class="rounded-2xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-50 text-left dark:bg-meta-4/40 border-b border-stroke dark:border-strokedark">
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-20">Ảnh</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sản phẩm</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Danh mục</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Giá bán</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tồn kho</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Trạng thái</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 text-center w-32">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stroke dark:divide-strokedark">
                            @forelse($products as $product)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-meta-4/10 transition-colors">
                                {{-- Cột Ảnh --}}
                                <td class="px-5 py-4.5">
                                    <div class="flex flex-col gap-2"> {{-- Thêm container này để tạo khoảng cách cách dòng nếu có nhiều ảnh --}}
                                        @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-xl object-cover ring-2 ring-gray-100 dark:ring-meta-4 mb-1">
                                        @else
                                        <div class="h-12 w-12 rounded-xl bg-gray-100 dark:bg-meta-4 flex items-center justify-center text-gray-400 dark:text-gray-500 shadow-inner">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                {{-- Cột Tên & Mô tả ngắn --}}
                                <td class="px-5 py-4.5">
                                    <div class="text-sm font-semibold text-black dark:text-white mb-0.5">{{ $product->name }}</div>
                                    @if($product->description)
                                    <div class="text-xs text-gray-400 truncate max-w-xs">{{ $product->description }}</div>
                                    @endif
                                </td>
                                {{-- Cột Danh mục --}}
                                <td class="px-5 py-4.5 text-sm font-medium text-gray-600 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-gray-100 px-2.5 py-1 text-xs dark:bg-meta-4 dark:text-gray-300">
                                        {{ $product->category->name ?? 'Mặc định' }}
                                    </span>
                                </td>
                                {{-- Cột Giá tiền --}}
                                <td class="px-5 py-4.5 text-sm font-bold text-black dark:text-white">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </td>
                                {{-- Cột Tồn kho --}}
                                <td class="px-5 py-4.5 text-sm font-medium">
                                    @if($product->stock <= 5)
                                        <span class="text-red-500 font-bold">{{ $product->stock }} (Sắp hết)</span>
                                        @else
                                        <span class="text-black dark:text-white">{{ $product->stock }}</span>
                                        @endif
                                </td>
                                {{-- Cột Trạng thái --}}
                                <td class="px-5 py-4.5">
                                    @if($product->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Đang bán
                                    </span>
                                    @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-500/10 dark:text-gray-400 dark:ring-gray-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                        Tạm ẩn
                                    </span>
                                    @endif
                                </td>
                                {{-- Cột Thao tác --}}
                                <td class="px-5 py-4.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="inline-flex items-center gap-1 rounded-xl border border-yellow-200 bg-yellow-50 px-3 py-1.5 text-xs font-semibold text-yellow-700 shadow-sm transition hover:bg-yellow-100 dark:border-yellow-500/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:hover:bg-yellow-500/20">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 shadow-sm transition hover:bg-red-100 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400 dark:hover:bg-red-500/20">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-4m-8 0H4"></path>
                                        </svg>
                                        <span>Không có sản phẩm nào tương thích hoặc dữ liệu trống.</span>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Khối phân trang dữ liệu --}}
                @if($products->hasPages())
                <div class="border-t border-stroke px-5 py-4 dark:border-strokedark bg-gray-50/30 dark:bg-meta-4/10">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- DIALOG/MODAL CHỈNH SỬA DANH MỤC --}}
<div id="editCategoryModal"
    class="fixed inset-0 z-[999] hidden items-center justify-center bg-black/60 backdrop-blur-sm transition-all">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-boxdark border border-stroke dark:border-strokedark m-4 transform transition-all">
        <div class="flex items-center justify-between mb-4 border-b border-stroke pb-3 dark:border-strokedark">
            <h3 class="text-lg font-bold text-black dark:text-white">Chỉnh sửa danh mục</h3>
            <button onclick="closeEditModal()" class="p-1 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-meta-4">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="editCategoryForm" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 dark:text-gray-400">Tên danh mục</label>
                <input type="text" id="editCategoryName" name="name" required placeholder="Tên danh mục"
                    class="w-full rounded-xl border border-stroke bg-transparent px-4 py-2.5 text-sm outline-none transition focus:border-primary dark:border-strokedark dark:text-white dark:focus:border-primary">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5 dark:text-gray-400">Mô tả lý do/thông tin</label>
                <textarea id="editCategoryDesc" name="description" rows="3" placeholder="Mô tả (tùy chọn)"
                    class="w-full rounded-xl border border-stroke bg-transparent px-4 py-2.5 text-sm outline-none transition focus:border-primary dark:border-strokedark dark:text-white dark:focus:border-primary"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 rounded-xl border border-stroke py-2.5 text-sm font-semibold text-black transition hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4">
                    Hủy thao tác
                </button>
                <button type="submit"
                    class="flex-1 rounded-xl bg-primary py-2.5 text-sm font-semibold text-white transition hover:bg-opacity-90 shadow-sm">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, desc) {
        document.getElementById('editCategoryForm').action = '/categories/' + id;
        document.getElementById('editCategoryName').value = name;
        document.getElementById('editCategoryDesc').value = desc;
        const modal = document.getElementById('editCategoryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('editCategoryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection