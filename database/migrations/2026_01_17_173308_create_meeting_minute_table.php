<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();

            $table->string('title');                 // Judul rapat
            $table->dateTime('meeting_at');          // Tanggal & jam rapat
            $table->string('location')->nullable();  // Lokasi rapat (opsional)

            $table->text('agenda')->nullable();      // Agenda singkat (opsional)
            $table->longText('content');             // Isi notulensi (wajib)

            $table->enum('status', ['draft','published'])->default('draft');

            // opsional, untuk nanti dukung filter “kedudukan”
            $table->string('kedudukan')->nullable(); // contoh: dpp_inti/bgkp/lingkungan/sekretariat

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
    }
};