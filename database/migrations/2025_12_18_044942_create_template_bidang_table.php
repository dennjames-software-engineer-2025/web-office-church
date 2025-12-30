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
        Schema::create('template_bidang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('templates')
                ->cascadeOnDelete();

            $table->foreignId('bidang_id')
                ->constrained('bidangs')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['template_id', 'bidang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_bidang');
    }
};
