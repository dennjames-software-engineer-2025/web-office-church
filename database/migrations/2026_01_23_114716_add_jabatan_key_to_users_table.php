<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Key untuk mengunci "jabatan unik"
            $table->string('jabatan_key')->nullable()->after('jabatan');
            $table->index('jabatan_key');

            /**
             * KUNCI UNIK:
             * - Kombinasi (kedudukan, jabatan_key, deleted_at)
             * - deleted_at ikut supaya: user yang soft-delete tidak mengunci lagi
             */
            $table->unique(['kedudukan', 'jabatan_key', 'deleted_at'], 'users_kedudukan_jabatankey_deletedat_unique');
        });

        // Backfill untuk data existing
        DB::table('users')
            ->whereNull('jabatan_key')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $u) {
                    $key = $u->jabatan;

                    if ($u->jabatan === 'ketua_bidang') {
                        $key = 'ketua_bidang#bidang:' . ($u->bidang_id ?? 'null');
                    } elseif ($u->jabatan === 'ketua_sie') {
                        $key = 'ketua_sie#bidang:' . ($u->bidang_id ?? 'null') . '#sie:' . ($u->sie_id ?? 'null');
                    }

                    DB::table('users')
                        ->where('id', $u->id)
                        ->update(['jabatan_key' => $key]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_kedudukan_jabatankey_deletedat_unique');
            $table->dropIndex(['jabatan_key']);
            $table->dropColumn('jabatan_key');
        });
    }
};
