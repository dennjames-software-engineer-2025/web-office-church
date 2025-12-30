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
        Schema::table('bidangs', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('nama_bidang');
            $table->index('is_active');
        });

        Schema::table('sies', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('nama_sie');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidangs', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_active');
        });

        Schema::table('sies', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_active');
        });
    }
};
