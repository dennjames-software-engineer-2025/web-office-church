<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidangs', function (Blueprint $table) {
            // data lama dianggap DPP Inti
            $table->string('kedudukan')->default('dpp_inti')->after('id');
            $table->index('kedudukan');
        });

        // jaga-jaga kalau ada null
        DB::table('bidangs')->whereNull('kedudukan')->update(['kedudukan' => 'dpp_inti']);
    }

    public function down(): void
    {
        Schema::table('bidangs', function (Blueprint $table) {
            $table->dropIndex(['kedudukan']);
            $table->dropColumn('kedudukan');
        });
    }
};