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
        Schema::table('product_language', function (Blueprint $table) {
            // Xóa cột 'content'
            $table->dropColumn('content');

            // Thêm cột 'price' và 'quantity'
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_language', function (Blueprint $table) {
            // Thêm lại cột 'content'
            $table->longText('content')->nullable();

            // Xóa cột 'price' và 'quantity'
            $table->dropColumn(['price', 'quantity']);
        });
    }
};