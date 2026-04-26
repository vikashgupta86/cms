<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();

            $table->string('title', 300);
            $table->string('slug', 300);

            $table->enum('type', ['content', 'file', 'external'])->default('content');

            $table->longText('body')->nullable();

            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_mime')->nullable();

            $table->string('external_url')->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');

            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['menu_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
