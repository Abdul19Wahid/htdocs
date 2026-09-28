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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // User who owns the category
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Category information
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();

            // Indicates whether this is a system/default category
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            // Prevent duplicate category names for the same user
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};