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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug', 191)->unique();
            $table->text('content')->nullable();
            $table->text('summary')->nullable();
            $table->text('source_url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('author')->nullable();
            $table->foreignId('news_source_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamp('published_at');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index('published_at');
            $table->index('author');
            $table->fulltext(['title', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
