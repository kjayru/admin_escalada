<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            // Logo exclusivo para la cabecera del landing del patrocinador
            $table->unsignedBigInteger('section_logo_media_id')->nullable()->after('circle_logo_media_id');
            $table->foreign('section_logo_media_id')->references('id')->on('media_files')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropForeign(['section_logo_media_id']);
            $table->dropColumn('section_logo_media_id');
        });
    }
};
