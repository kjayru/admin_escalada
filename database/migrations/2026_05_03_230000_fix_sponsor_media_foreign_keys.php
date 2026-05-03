<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Los FKs originales de los campos media de sponsors apuntaban a la tabla `legacy_media`
 * (registrada como `media` en las migraciones originales).
 * El MediaPicker de Slimani guarda IDs en `media_files`, lo que viola el constraint.
 * Esta migración elimina los FKs obsoletos; las relaciones se gestionan desde Eloquent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropForeign(['logo_media_id']);
            $table->dropForeign(['slide_image_media_id']);
            $table->dropForeign(['gallery_1_media_id']);
            $table->dropForeign(['gallery_2_media_id']);
            $table->dropForeign(['gallery_3_media_id']);
            $table->dropForeign(['gallery_4_media_id']);
            $table->dropForeign(['contact_media_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->foreign('logo_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('slide_image_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('gallery_1_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('gallery_2_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('gallery_3_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('gallery_4_media_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('contact_media_id')->references('id')->on('media')->nullOnDelete();
        });
    }
};
