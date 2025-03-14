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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id', 191)->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('news_source_id', 191)->nullable()->constrained()->onDelete('cascade');
            $table->string('preferred_author', 191)->nullable();
            $table->timestamps();

            // Ensure unique combinations
            $table->unique(['user_id', 'category_id']);
            $table->unique(['user_id', 'news_source_id']);
            $table->unique(['user_id', 'preferred_author']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
