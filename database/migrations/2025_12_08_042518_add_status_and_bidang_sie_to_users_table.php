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
        Schema::table('users', function (Blueprint $table) {
            $table  ->string('status')
                    ->default('pending')
                    ->after('password');
            
            $table  ->foreignId('bidang_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('bidangs')
                    ->nullOnDelete();

            $table  ->foreignId('sie_id')
                    ->nullable()
                    ->after('bidang_id')
                    ->constrained('sies')
                    ->nullOnDelete();
        });
    }

    // Keterangan
    // 1. Menggunakan nullOnDelete, supaya ketika bidang dihapus, user tidak ikut terhapus melainkan bidang_id terisi 0

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['bidang_id']);
            $table->dropForeign(['sie_id']);
            $table->dropColumn(['bidang_id', 'sie_id', 'status']);
        });
    }
};
