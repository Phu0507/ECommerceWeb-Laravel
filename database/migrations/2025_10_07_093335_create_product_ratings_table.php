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
        Schema::create('product_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->tinyInteger('rating')->default(1); // 1 đến 5 sao
            $table->text('comment')->nullable();      // nội dung đánh giá
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ratings');
    }
};