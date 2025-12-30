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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table  ->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            
            $table  ->foreignId('bidang_id')
                    ->nullable()
                    ->constrained('bidangs')
                    ->nullOnDelete();
                
            $table  ->foreignId('sie_id')
                    ->nullable()
                    ->constrained('sies')
                    ->nullOnDelete();
            
            $table->string('title');
            $table->string('filename');
            $table->string('path');
            $table->string('file_type');
            $table->text('description')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
