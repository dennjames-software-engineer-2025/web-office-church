<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('proposal_files', function (Blueprint $table) {
            if (!Schema::hasColumn('proposal_files', 'deleted_at')) {
                $table->softDeletes(); // adds deleted_at
            }
        });
    }

    public function down(): void
    {
        Schema::table('proposal_files', function (Blueprint $table) {
            if (Schema::hasColumn('proposal_files', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
