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
        Schema::create('pengesahan_dokumens', function (Blueprint $table) {
            $table->id();

            // Pengirim dokumen
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // File asli yg diajukan
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();

            // Metadata pengajuan
            $table->string('tujuan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan_ditolak')->nullable();

            // Metadata pengesahan
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();

            // Output hasil proses
            $table->string('surat_path')->nullable();       // file PDF Surat Pengesahan
            $table->string('watermarked_path')->nullable(); // hasil watermark PDF

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengesahan_dokumens');
    }
};
