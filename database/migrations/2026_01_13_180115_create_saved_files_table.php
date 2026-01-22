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
        Schema::create('saved_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();

            // polymorphic: source_type + source_id
            $table->string('source_type'); // contoh: App\Models\Template atau App\Models\ProposalFile
            $table->unsignedBigInteger('source_id');

            $table->string('title_override')->nullable();

            // kalau true: hanya sekretaris + super_admin
            $table->boolean('is_private')->default(true);

            // share rules (simple, tanpa tabel pivot biar gampang dipahami)
            // contoh isi:
            // shared_roles: ["ketua","bendahara"]
            // shared_bidang_ids: [1,2,3]
            $table->json('shared_roles')->nullable();
            $table->json('shared_bidang_ids')->nullable();

            $table->foreignId('added_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_files');
    }
};
