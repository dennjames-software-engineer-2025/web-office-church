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
        Schema::table('users', function (Blueprint $table) {

            // Menghapus unique index lama di kolom email 
            $table->dropUnique('users_email_unique');

            // Membuat unique index baru
            $table->unique(['email', 'deleted_at'], 'users_email_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus index kombinasi
            $table->dropUnique('users_email_deleted_at_unique');

            // Balik ke unique email
            $table->unique('email', 'users_email_unique');
        });
    }
};
