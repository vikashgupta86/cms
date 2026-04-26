<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

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
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('thumbnail', 500)->nullable();

            $table->string('meta_title', 300)->nullable();
            $table->text('meta_description')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['menu_item_id', 'status', 'sort_order'], 'idx_contents_menu_status_order');
            $table->index(['menu_item_id', 'published_at'], 'idx_contents_menu_published');
            $table->index(['status', 'created_at'], 'idx_contents_status_created');
            $table->index(['type'], 'idx_contents_type');
            $table->unique(['menu_item_id', 'slug'], 'uq_contents_menu_item_slug');
        });

        try {
            DB::statement('ALTER TABLE contents ADD FULLTEXT ft_contents_search (title, body)');
        } catch (\Throwable $e) {
            // Ignore on engines that do not support FULLTEXT for this setup.
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
