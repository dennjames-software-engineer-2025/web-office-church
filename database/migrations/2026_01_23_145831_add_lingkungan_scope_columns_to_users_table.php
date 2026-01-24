<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // khusus kedudukan=lingkungan
            $table->string('lingkungan_scope')->nullable()->after('kedudukan'); // wilayah|lingkungan
            $table->string('wilayah')->nullable()->after('lingkungan_scope');   // wilayah_1..wilayah_7
            $table->string('lingkungan')->nullable()->after('wilayah');        // nama lingkungan (32 opsi)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['lingkungan_scope','wilayah','lingkungan']);
        });
    }
};
