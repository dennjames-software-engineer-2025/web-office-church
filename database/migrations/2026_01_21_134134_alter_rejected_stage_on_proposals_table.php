<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Pastikan doctrine/dbal terinstall kalau pakai change():
            // composer require doctrine/dbal
            $table->string('rejected_stage', 30)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('rejected_stage', 10)->nullable()->change();
        });
    }
};
