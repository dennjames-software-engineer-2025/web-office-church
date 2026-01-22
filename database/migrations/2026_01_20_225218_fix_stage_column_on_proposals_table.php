<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * MASALAH:
         * - Kolom `stage` masih ENUM lama (misal: 'ketua','bendahara_1','bendahara_2')
         * - Flow baru butuh nilai: 'sie','ketua_bidang','dpp_harian','romo','bendahara'
         *
         * SOLUSI AMAN:
         * - Ubah `stage` jadi VARCHAR agar fleksibel (tidak gampang “data truncated” lagi).
         * - Set default ke 'ketua_bidang' sesuai flow baru.
         *
         * Catatan:
         * - Ini pakai raw SQL, jadi TIDAK butuh doctrine/dbal.
         */

        // Pastikan kolom stage jadi VARCHAR dan default sesuai flow baru
        DB::statement("ALTER TABLE `proposals` MODIFY `stage` VARCHAR(30) NOT NULL DEFAULT 'ketua_bidang'");
    }

    public function down(): void
    {
        /**
         * Biasanya rollback tidak dipakai di sistem yang sudah berjalan.
         * Kalau kamu mau, bisa kamu set balik ke enum versi lama.
         * Tapi hati-hati: data stage baru bisa jadi tidak cocok dengan enum lama.
         */
    }
};
