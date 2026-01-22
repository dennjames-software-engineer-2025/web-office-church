<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Ubah jadi string agar aman untuk nilai seperti 'romo', 'dpp_harian', dll
            // Catatan: ->change() butuh doctrine/dbal jika kolom sudah ada.
            $table->string('rejected_stage', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // balikkan ke string nullable juga (biar aman)
            $table->string('rejected_stage', 50)->nullable()->change();
        });
    }
};
