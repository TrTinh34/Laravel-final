@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-black dark:text-white">Quản lý Sản phẩm</h2>
        <a href="{{ route('products.create') }}"
            style="background-color:#3C50E0;color:#ffffff;"
            class="rounded-lg px-4 py-2 text-sm font-medium hover:opacity-90">
            + Thêm sản phẩm
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="flex gap-6">

        {{-- SIDEBAR DANH MỤC --}}
        <div class="w-72 shrink-0 space-y-4">

            {{-- Form thêm danh mục --}}
            <div class="rounded-xl border border-stroke bg-white p-4 shadow-sm dark:border-strokedark dark:bg-boxdark">
                <h3 class="mb-3 font-semibold text-black dark:text-white">+ Thêm danh mục</h3>
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <input type="text" name="name" placeholder="Tên danh mục..."
                        required
                        class="mb-2 w-full rounded-lg border border-stroke bg-transparent px-3 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    <textarea name="description" placeholder="Mô tả (tùy chọn)" rows="2"
                        class="mb-2 w-full rounded-lg border border-stroke bg-transparent px-3 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white"></textarea>
                    <button type="submit"
                        style="background-color:#3C50E0;color:#ffffff;"
                        class="w-full rounded-lg py-2 text-sm font-medium hover:opacity-90">
                        Thêm
                    </button>
                </form>
            </div>

            {{-- Danh sách danh mục --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-4 py-3 dark:border-strokedark">
                    <h3 class="font-semibold text-black dark:text-white">Danh mục</h3>
                </div>
                <ul class="divide-y divide-stroke dark:divide-strokedark">
                    {{-- Tất cả --}}
                    <li>
                        <a href="{{ route('products.index') }}"
                           class="flex items-center justify-between px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-meta-4
                                  {{ !$selectedCategory ? 'font-bold text-primary' : 'text-black dark:text-white' }}">
                            <span>📦 Tất cả sản phẩm</span>
                            <span class="text-xs text-gray-400">{{ $categories->sum('products_count') }}</span>
                        </a>
                    </li>
                    @foreach($categories as $cat)
                    <li class="group relative">
                        <a href="{{ route('products.index', ['category_id' => $cat->id]) }}"
                           class="flex items-center justify-between px-4 py-3 text-sm hover:bg-gray-50 dark:hover:bg-meta-4
                                  {{ $selectedCategory == $cat->id ? 'font-bold text-primary' : 'text-black dark:text-white' }}">
                            <span>{{ $cat->name }}</span>
                            <span class="text-xs text-gray-400">{{ $cat->products_count }}</span>
                        </a>

                        {{-- Nút sửa/xóa --}}
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 hidden items-center gap-1 group-hover:flex">
                            {{-- Nút sửa --}}
                            <button onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}')"
                                class="rounded bg-yellow-100 px-2 py-1 text-xs text-yellow-700 hover:bg-yellow-200">
                                ✏️
                            </button>
                            {{-- Nút xóa --}}
                            <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                                  onsubmit="return confirm('Xóa danh mục này?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="rounded bg-red-100 px-2 py-1 text-xs text-red-700 hover:bg-red-200">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- NỘI DUNG CHÍNH: BẢNG SẢN PHẨM --}}
        <div class="flex-1 min-w-0">

            {{-- Thanh tìm kiếm --}}
            <div class="mb-4 rounded-xl border border-stroke bg-white p-4 shadow-sm dark:border-strokedark dark:bg-boxdark">
                <form method="GET" action="{{ route('products.index') }}" class="flex gap-3">
                    @if($selectedCategory)
                        <input type="hidden" name="category_id" value="{{ $selectedCategory }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm kiếm tên sản phẩm..."
                        class="flex-1 rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    <button type="submit"
                        style="background-color:#3C50E0;color:#ffffff;"
                        class="rounded-lg px-5 py-2 text-sm font-medium hover:opacity-90">
                        🔍 Tìm
                    </button>
                    <a href="{{ route('products.index', $selectedCategory ? ['category_id' => $selectedCategory] : []) }}"
                        class="rounded-lg border border-stroke px-4 py-2 text-sm text-black hover:bg-gray-100
                               dark:border-strokedark dark:text-white dark:hover:bg-meta-4">
                        ✖
                    </a>
                </form>
            </div>

            {{-- Bảng --}}
            <div class="rounded-xl border border-stroke bg-white shadow-sm dark:border-strokedark dark:bg-boxdark">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white w-16">Ảnh</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Tên sản phẩm</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Danh mục</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Giá</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Tồn kho</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Trạng thái</th>
                                <th class="px-4 py-4 text-sm font-semibold text-black dark:text-white">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr class="border-t border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4">
                                <td class="px-4 py-3">
                                    @if($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt=""
                                             class="h-12 w-12 rounded-lg object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 text-xs">
                                            N/A
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-black dark:text-white">{{ $product->name }}</div>
                                    @if($product->description)
                                        <div class="text-xs text-gray-400 truncate max-w-xs">{{ $product->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $product->category->name ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-black dark:text-white">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </td>
                                <td class="px-4 py-3 text-sm text-black dark:text-white">
                                    {{ $product->stock }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($product->is_active)
                                        <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                            Đang bán
                                        </span>
                                    @else
                                        <span class="inline-block rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                            Ẩn
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('products.edit', $product) }}"
                                            class="rounded bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-700 hover:bg-yellow-200">
                                            Sửa
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm('Xóa sản phẩm này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded bg-red-100 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-200">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                                    Không có sản phẩm nào.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                <div class="border-t border-stroke px-4 py-4 dark:border-strokedark">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL SỬA DANH MỤC --}}
<div id="editCategoryModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl dark:bg-boxdark">
        <h3 class="mb-4 text-lg font-semibold text-black dark:text-white">Sửa danh mục</h3>
        <form id="editCategoryForm" method="POST">
            @csrf @method('PUT')
            <input type="text" id="editCategoryName" name="name" required
                placeholder="Tên danh mục"
                class="mb-3 w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                       outline-none focus:border-primary dark:border-strokedark dark:text-white">
            <textarea id="editCategoryDesc" name="description" rows="3"
                placeholder="Mô tả (tùy chọn)"
                class="mb-4 w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                       outline-none focus:border-primary dark:border-strokedark dark:text-white"></textarea>
            <div class="flex gap-3">
                <button type="submit"
                    style="background-color:#3C50E0;color:#ffffff;"
                    class="flex-1 rounded-lg py-2 text-sm font-medium hover:opacity-90">
                    Lưu thay đổi
                </button>
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 rounded-lg border border-stroke py-2 text-sm text-black
                           hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4">
                    Hủy
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