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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();

            // Relasi Utama
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Metadata Proposal 
            $table->string('judul');
            $table->text('tujuan');

            // Status Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Alasan ketik Ditolak
            $table->text('alasan_ditolak')->nullable();

            // Pengesah 
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
