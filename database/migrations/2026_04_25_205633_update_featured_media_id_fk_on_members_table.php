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
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['featured_media_id']);
            $table->foreign('featured_media_id')
                  ->references('id')
                  ->on('media_files')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['featured_media_id']);
            $table->foreign('featured_media_id')
                  ->references('id')
                  ->on('media')
                  ->nullOnDelete();
        });
    }
};
