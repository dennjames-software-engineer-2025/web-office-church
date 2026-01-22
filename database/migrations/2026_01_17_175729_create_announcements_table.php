<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('body');

            $table->string('status')->default('published'); // draft | published
            $table->boolean('is_pinned')->default(false);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Targeting (opsional)
            $table->json('target_kedudukan')->nullable(); // ["dpp_inti","bgkp"]
            $table->json('target_roles')->nullable();     // ["ketua","sekretaris"]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};