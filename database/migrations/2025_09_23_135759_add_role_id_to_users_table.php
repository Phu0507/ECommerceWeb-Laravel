<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Xoá ràng buộc cũ
            $table->dropForeign(['role_id']);

            // Set default
            $table->unsignedBigInteger('role_id')->default(2)->change();

            // Tạo lại foreign key
            $table->foreign('role_id')
                  ->references('id')
                  ->on('user_roles')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->unsignedBigInteger('role_id')->change();
            $table->foreign('role_id')
                  ->references('id')
                  ->on('user_roles')
                  ->cascadeOnDelete();
        });
    }
};