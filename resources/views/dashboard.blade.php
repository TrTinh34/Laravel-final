@extends('layouts.app')

@section('content')
  <div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12 space-y-6 xl:col-span-12">
      {{-- Truyền dữ liệu trực tiếp vào component qua các attribute --}}
      <x-ecommerce.ecommerce-metrics 
        :customers="$totalCustomers" 
        :orders="$totalOrders" 
        :revenue="$totalRevenue" 
      />
    </div>
  </div>
@endsection
