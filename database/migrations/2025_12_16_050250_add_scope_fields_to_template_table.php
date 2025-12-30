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
        Schema::table('templates', function (Blueprint $table) {
            $table->foreignId('bidang_id')
                ->nullable()
                ->after('uploaded_by')
                ->constrained('bidangs')
                ->nullOnDelete();
            
            $table->boolean('share_to_bidang')
                ->default(false)
                ->after('bidang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropForeign(['bidang_id']);
            $table->dropColumn(['bidang_id', 'share_to_bidang']);
        });
    }
};
