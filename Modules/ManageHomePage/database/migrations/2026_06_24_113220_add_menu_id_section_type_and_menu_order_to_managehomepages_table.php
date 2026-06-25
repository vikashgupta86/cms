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
        Schema::table('managehomepages', function (Blueprint $table) {
            $table->foreignId('menu_id')->nullable()->after('id')->constrained('menu_items')->onDelete('cascade');
            $table->string('section_type', 100)->nullable()->after('menu_id');
            $table->integer('menu_order')->default(0)->after('section_type');
            $table->string('name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('managehomepages', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->dropColumn(['menu_id', 'section_type', 'menu_order']);
            $table->string('name')->nullable(false)->change();
        });
    }
};
