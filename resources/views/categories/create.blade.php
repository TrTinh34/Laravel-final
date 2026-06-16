@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">Thêm Danh Mục Mới</h2>
        <a href="{{ route('categories.index') }}" class="text-sm font-medium text-primary hover:underline"><- Quay lại danh sách</a>
    </div>

    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <form action="{{ route('categories.store') }}" method="POST" class="p-6.5">
            @csrf
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black dark:text-white">Tên danh mục <span class="text-meta-1">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập tên danh mục..." class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary dark:border-form-strokedark dark:bg-form-input text-black dark:text-white" />
                @error('name') <p class="text-meta-1 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium">
                Lưu danh mục
            </button>
        </form>
    </div>
</div>
@endsection