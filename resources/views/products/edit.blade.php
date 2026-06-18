@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-2xl p-4 md:p-6 2xl:p-10">

    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('products.index') }}" class="text-sm text-gray-500 hover:text-primary">← Quay lại</a>
        <h2 class="text-2xl font-bold text-black dark:text-white">Sửa sản phẩm</h2>
    </div>

    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-stroke bg-white p-6 shadow-sm dark:border-strokedark dark:bg-boxdark">
        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Tên sản phẩm --}}
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">
                    Tên sản phẩm <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Danh mục --}}
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Danh mục</label>
                <select name="category_id"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white dark:bg-boxdark">
                    <option value="">-- Không có danh mục --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mô tả --}}
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Mô tả</label>
                <textarea name="description" rows="4"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Giá & Tồn kho --}}
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">
                        Giá (vnđ) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}"
                        min="0" required
                        class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    @error('price')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">
                        Tồn kho <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                        min="0" required
                        class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                               outline-none focus:border-primary dark:border-strokedark dark:text-white">
                    @error('stock')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Hình ảnh --}}
            <div class="mb-4">
                <label class="mb-2 block text-sm font-medium text-black dark:text-white">Hình ảnh</label>

                {{-- Preview ảnh hiện tại --}}
                @if($product->image)
                    <div class="mb-3 flex items-center gap-4">
                        <img src="{{ Storage::url($product->image) }}"
                             alt="{{ $product->name }}"
                             id="currentImage"
                             class="h-24 w-24 rounded-lg object-cover border border-stroke">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ảnh hiện tại</p>
                            <p class="text-xs text-gray-400 mt-1">Chọn ảnh mới bên dưới để thay thế</p>
                        </div>
                    </div>
                @endif

                {{-- Preview ảnh mới --}}
                <div id="newImagePreview" class="mb-3 hidden">
                    <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Ảnh mới:</p>
                    <img id="previewImg" src="" alt="preview"
                         class="h-24 w-24 rounded-lg object-cover border border-primary">
                </div>

                <input type="file" name="image" accept="image/*" id="imageInput"
                    onchange="previewImage(this)"
                    class="w-full rounded-lg border border-stroke bg-transparent px-4 py-2 text-sm
                           outline-none focus:border-primary dark:border-strokedark dark:text-white">
                <p class="mt-1 text-xs text-gray-400">Định dạng: JPG, PNG, GIF. Tối đa 2MB.</p>
                @error('image')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trạng thái --}}
            <div class="mb-6 flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-stroke accent-primary">
                <label for="is_active" class="text-sm font-medium text-black dark:text-white">
                    Đang bán (hiển thị cho khách hàng)
                </label>
            </div>

            {{-- Nút hành động --}}
            <div class="flex gap-3">
                <button type="submit"
                    style="background-color:#3C50E0;color:#ffffff;"
                    class="rounded-lg px-6 py-2 text-sm font-medium hover:opacity-90 transition">
                    💾 Cập nhật sản phẩm
                </button>
                <a href="{{ route('products.index') }}"
                    class="rounded-lg border border-stroke px-6 py-2 text-sm font-medium
                           text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4">
                    Hủy
                </a>
            </div>

        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('newImagePreview');
    const previewImg = document.getElementById('previewImg');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endsection