<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            // Logo circular para el slider de la Home (independiente del logo rectangular)
            $table->unsignedBigInteger('circle_logo_media_id')->nullable()->after('logo_media_id');
            $table->foreign('circle_logo_media_id')->references('id')->on('media_files')->nullOnDelete();

            // Imagen destacada para el box "¿Te gustó este producto?"
            $table->unsignedBigInteger('highlight_media_id')->nullable()->after('gallery_4_media_id');
            $table->foreign('highlight_media_id')->references('id')->on('media_files')->nullOnDelete();

            // URL del botón "Comprar aquí" en el landing del patrocinador
            $table->string('buy_url')->nullable()->after('website_url');
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropForeign(['circle_logo_media_id']);
            $table->dropForeign(['highlight_media_id']);
            $table->dropColumn(['circle_logo_media_id', 'highlight_media_id', 'buy_url']);
        });
    }
};
