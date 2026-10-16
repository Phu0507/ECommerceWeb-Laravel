<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Xóa cột cũ nếu tồn tại
            if (Schema::hasColumn('orders', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // Thêm lại session_id kiểu string
            $table->string('session_id', 100)->nullable()->after('customer_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // Quay về kiểu uuid
            $table->uuid('session_id')->nullable()->after('customer_id')->index();
        });
    }
};