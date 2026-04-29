<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->string('contentable_type')->nullable()->after('page_id');
            $table->unsignedBigInteger('contentable_id')->nullable()->after('contentable_type');
            $table->index(['contentable_type', 'contentable_id'], 'page_sections_contentable_index');
        });

        // Migrar datos existentes al nuevo esquema polimórfico
        DB::table('page_sections')
            ->whereNotNull('page_id')
            ->update([
                'contentable_type' => 'App\\Models\\Page',
                'contentable_id'   => DB::raw('page_id'),
            ]);

        // Hacer page_id nullable (deprecated, se mantiene para compatibilidad)
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->unsignedBigInteger('page_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('page_sections', function (Blueprint $table) {
            $table->dropIndex('page_sections_contentable_index');
            $table->dropColumn(['contentable_type', 'contentable_id']);
        });

        Schema::table('page_sections', function (Blueprint $table) {
            $table->foreignId('page_id')->nullable(false)->change()->constrained()->cascadeOnDelete();
        });
    }
};
