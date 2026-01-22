<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // 1) Lepas ketergantungan Program
            if (Schema::hasColumn('proposals', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }

            // 2) Relasi pengajuan: bidang & sie pengaju
            if (!Schema::hasColumn('proposals', 'bidang_id')) {
                $table->foreignId('bidang_id')->nullable()->after('created_by')
                    ->constrained('bidangs')->nullOnDelete();
            }

            if (!Schema::hasColumn('proposals', 'sie_id')) {
                $table->foreignId('sie_id')->nullable()->after('bidang_id')
                    ->constrained('sies')->nullOnDelete();
            }

            // 3) Flow baru
            // stage: sie -> ketua_bidang -> dpp_harian -> romo -> bendahara
            if (!Schema::hasColumn('proposals', 'stage')) {
                $table->enum('stage', ['sie', 'ketua_bidang', 'dpp_harian', 'romo', 'bendahara'])
                    ->default('ketua_bidang')
                    ->after('status');
            } else {
                // kalau stage lama ada (ketua/bendahara_1/bendahara_2), biarkan dulu atau ubah manual di DB
                // aman: kamu bisa ubah nanti kalau sudah siap (karena enum lama bisa beda).
            }

            // status baru
            if (Schema::hasColumn('proposals', 'status')) {
                // kalau enum lama: ['review','diterima','ditolak'] → kita butuh lebih detail
                // supaya aman lintas DB, kita pakai string (lebih fleksibel).
                $table->string('status', 50)->default('menunggu_ketua_bidang')->change();
            } else {
                $table->string('status', 50)->default('menunggu_ketua_bidang')->after('tujuan');
            }

            // Deadline DPP Harian (default 3 hari), bisa dikustom sekretaris
            if (!Schema::hasColumn('proposals', 'dpp_harian_until')) {
                $table->timestamp('dpp_harian_until')->nullable()->after('stage');
            }

            // Notes sekretaris ke Romo
            if (!Schema::hasColumn('proposals', 'notes')) {
                $table->text('notes')->nullable()->after('dpp_harian_until');
            }

            // Approval Ketua Bidang
            if (!Schema::hasColumn('proposals', 'ketua_bidang_approved_by')) {
                $table->foreignId('ketua_bidang_approved_by')->nullable()->after('notes')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('proposals', 'ketua_bidang_approved_at')) {
                $table->timestamp('ketua_bidang_approved_at')->nullable()->after('ketua_bidang_approved_by');
            }

            // Approval Romo (Ketua DPP Inti)
            if (!Schema::hasColumn('proposals', 'romo_approved_by')) {
                $table->foreignId('romo_approved_by')->nullable()->after('ketua_bidang_approved_at')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('proposals', 'romo_approved_at')) {
                $table->timestamp('romo_approved_at')->nullable()->after('romo_approved_by');
            }

            // Nomor proposal (generate saat approved)
            if (!Schema::hasColumn('proposals', 'proposal_no')) {
                $table->string('proposal_no', 30)->nullable()->unique()->after('romo_approved_at');
            }

            // Receipt PDF path (optional)
            if (!Schema::hasColumn('proposals', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('proposal_no');
            }

            // Reject info (kamu sudah punya reject_reason dkk; kita pakai itu)
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // rollback minimal (optional)
            // kamu bisa isi sesuai kebutuhan, tapi biasanya di project live rollback jarang.
        });
    }
};