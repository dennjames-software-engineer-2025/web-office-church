<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_bidang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->foreignId('bidang_id')
                ->constrained('bidangs')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['document_id', 'bidang_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_bidang');
    }
};