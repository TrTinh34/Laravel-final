@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-primary">← Quay lại</a>
        <h2 class="text-2xl font-bold text-black dark:text-white">Thêm sản phẩm mới</h2>
    </div>

    <div class="rounded-xl border border-stroke bg-white p-6 shadow-sm dark:border-strokedark dark:bg-boxdark">
        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Tên sản phẩm *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Danh mục</label>
                <select name="category_id"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
                    <option value="">-- Không có danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Mô tả</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">Giá (đ) *</label>
                    <input type="number" name="price" value="{{ old('price', 0) }}" min="0" required
                        class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    @error('price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">Tồn kho *</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required
                        class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    @error('stock')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Hình ảnh</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">
                @error('image')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-stroke">
                <label for="is_active" class="text-sm text-black dark:text-white">Đang bán</label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    style="background-color:#3C50E0;color:#ffffff;"
                    class="rounded-lg px-6 py-2 text-sm font-medium hover:opacity-90">
                    Thêm sản phẩm
                </button>
                <a href="{{ route('products.index') }}"
                    class="rounded-lg border border-stroke px-6 py-2 text-sm text-black
                           hover:bg-gray-100 dark:border-strokedark dark:text-white">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection