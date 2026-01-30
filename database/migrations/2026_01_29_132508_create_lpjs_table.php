<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lpjs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('proposal_id')->constrained('proposals')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // simpan snapshot bidang/sie (biar gampang filter role)
            $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
            $table->foreignId('sie_id')->nullable()->constrained('sies')->nullOnDelete();

            $table->string('status', 50)->default('menunggu_ketua_bidang'); // alur sama proposal
            $table->string('stage', 50)->default('ketua_bidang');

            $table->text('notes')->nullable();

            $table->text('reject_reason')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejected_stage', 50)->nullable();

            $table->foreignId('ketua_bidang_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ketua_bidang_approved_at')->nullable();

            // final approve (Ketua DPP / Sekretaris DPP)
            $table->foreignId('final_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('final_approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // opsional: cegah ketua sie bikin LPJ kalau proposal belum approved (akan kita enforce di controller)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpjs');
    }
};
