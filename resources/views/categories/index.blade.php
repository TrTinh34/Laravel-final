@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">Quản lý Danh mục</h2>
        <a href="{{ route('categories.create') }}" class="inline-flex items-center justify-center gap-2.5 rounded-md bg-primary py-3 px-6 text-center font-medium shadow-md">
            <span>+</span> <span>Thêm danh mục mới</span>
        </a>
    </div>

    @if(session('success'))
        <div id="success-alert" class="mb-6 flex w-full border-l-6 border-[#34D399] bg-[#34D399] bg-opacity-[15%] px-7 py-4 shadow-md transition-opacity duration-500">
            <p class="text-base text-[#1d825c] dark:text-[#34D399]">{{ session('success') }}</p>
        </div>
    @endif

    <div class="rounded-sm border border-stroke bg-white px-5 pt-6 pb-2.5 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-4 px-4 font-semibold text-black dark:text-white xl:pl-11">ID</th>
                        <th class="py-4 px-4 font-semibold text-black dark:text-white">Tên danh mục</th>
                        <th class="py-4 px-4 font-semibold text-black dark:text-white">Slug Đường dẫn</th>
                        <th class="py-4 px-4 font-semibold text-black dark:text-white text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="border-b border-stroke dark:border-strokedark">
                        <td class="py-5 px-4 pl-9 xl:pl-11">
                            <p class="text-black dark:text-white">{{ $category->id }}</p>
                        </td>
                        <td class="py-5 px-4">
                            <p class="text-black dark:text-white font-medium">{{ $category->name }}</p>
                        </td>
                        <td class="py-5 px-4">
                            <p class="text-black dark:text-white">{{ $category->slug }}</p>
                        </td>
                        <td class="py-5 px-4">
                            <div class="flex items-center justify-center space-x-4">
                                <a href="{{ route('categories.edit', $category->id) }}" class="font-medium text-primary hover:underline">Sửa</a>
                                <div>/</div>
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa danh mục này?')" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-meta-1 hover:underline bg-transparent border-0 p-0 cursor-pointer">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-8 text-black dark:text-white">Chưa có danh mục nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 mb-4">{{ $categories->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const alert = document.getElementById('success-alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    }
</script>
@endpush