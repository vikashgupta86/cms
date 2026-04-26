<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            // Links to menu_items (the leaf/section node this content belongs to)
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->cascadeOnDelete();

            $table->string('title', 300);
            $table->string('slug', 300);
            $table->enum('type', ['content', 'file', 'external'])->default('content');

            $table->longText('body')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name', 300)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_mime', 150)->nullable();
            $table->string('external_url', 1000)->nullable();

            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->string('meta_title', 300)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords', 500)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('menu_item_id');
            $table->index('slug');
            $table->index('status');
            $table->index('type');
            $table->index('sort_order');
            $table->index('published_at');
            $table->unique(['menu_item_id', 'slug'], 'uq_contents_menuitem_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
