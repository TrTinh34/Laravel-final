<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo tài khoản Admin
        User::create([
            'name' => 'Hệ thống Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
        ]);

        // Tạo tài khoản Editor
        User::create([
            'name' => 'Biên tập viên Editor',
            'email' => 'editor@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'editor',
        ]);

        // Tạo tài khoản Khách hàng mẫu
        User::create([
            'name' => 'Nguyễn Văn Khách',
            'email' => 'khachhang@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
        ]);
    }
}
