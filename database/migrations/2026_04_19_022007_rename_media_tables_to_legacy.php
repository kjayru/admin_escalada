<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('media', 'legacy_media');

        if (Schema::hasTable('mediables')) {
            Schema::rename('mediables', 'legacy_mediables');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('legacy_mediables')) {
            Schema::rename('legacy_mediables', 'mediables');
        }

        Schema::rename('legacy_media', 'media');
    }
};
