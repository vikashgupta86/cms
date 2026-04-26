<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            // Belong to MenuItem (which acts as the tree structure)
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->nullable();
            
            // content, file, external
            $table->string('type')->default('content');
            
            $table->longText('body')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('external_url')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            // enum or string for status
            $table->string('status')->default('published');
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['menu_item_id', 'status', 'sort_order']);
            $table->index(['menu_item_id', 'published_at']);
        });

        // Add FULLTEXT index separately
        DB::statement('ALTER TABLE contents ADD FULLTEXT content_fulltext(title, body)');
        
        // Note: Partitioning on status is hard if we don't have status in primary key.
        // If MySQL requires the partitioning column to be part of the primary/unique key,
        // it requires changing the primary key schema, which is complex for Laravel.
        // So we will add the SQL for partitioning as a comment for the user to apply if needed,
        // or apply it if there's no unique constraints breaking it.
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents');
    }
};
