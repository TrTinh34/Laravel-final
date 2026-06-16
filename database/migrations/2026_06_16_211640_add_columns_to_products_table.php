<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            Schema::table('products', function (Blueprint $table) {
                $table->integer('stock')->default(0)->after('price');
                $table->boolean('is_active')->default(true)->after('image');
                $table->string('slug')->unique()->nullable()->after('name');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['stock', 'is_active', 'slug']);
            });
        });
    }
};
