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
        Schema::table('sponsors', function (Blueprint $table) {
            // Tagline / descripción corta
            $table->string('tagline')->nullable()->after('description');

            // Galería del slider (hasta 4 imágenes)
            $table->foreignId('gallery_1_media_id')->nullable()->after('slide_image_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('gallery_2_media_id')->nullable()->after('gallery_1_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('gallery_3_media_id')->nullable()->after('gallery_2_media_id')->constrained('media')->nullOnDelete();
            $table->foreignId('gallery_4_media_id')->nullable()->after('gallery_3_media_id')->constrained('media')->nullOnDelete();

            // Tarjeta del representante del sponsor
            $table->string('contact_name')->nullable()->after('gallery_4_media_id');
            $table->string('contact_title')->nullable()->after('contact_name');
            $table->text('contact_text')->nullable()->after('contact_title');
            $table->foreignId('contact_media_id')->nullable()->after('contact_text')->constrained('media')->nullOnDelete();

            // Redes sociales
            $table->string('facebook_url')->nullable()->after('contact_media_id');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('email')->nullable()->after('twitter_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            //
        });
    }
};
