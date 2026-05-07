<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El FK original del campo banner_media_id en sponsor_placements apuntaba a la tabla `legacy_media`
 * (registrada como `media` en las migraciones originales).
 * El MediaPicker de Slimani guarda IDs en `media_files`, lo que viola el constraint.
 * Esta migración elimina el FK obsoleto; las relaciones se gestionan desde Eloquent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsor_placements', function (Blueprint $table) {
            // Eliminar usando el nombre completo del constraint
            $table->dropForeign('sponsor_placements_banner_media_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::table('sponsor_placements', function (Blueprint $table) {
            $table->foreign('banner_media_id')->references('id')->on('media')->nullOnDelete();
        });
    }
};
