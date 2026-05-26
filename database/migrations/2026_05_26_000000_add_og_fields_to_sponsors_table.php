<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('status');
            $table->text('og_description')->nullable()->after('og_title');
            $table->unsignedBigInteger('og_image_media_id')->nullable()->after('og_description');
            $table->unsignedSmallInteger('og_image_width')->nullable()->after('og_image_media_id');
            $table->unsignedSmallInteger('og_image_height')->nullable()->after('og_image_width');
        });
    }

    public function down(): void
    {
        Schema::table('sponsors', function (Blueprint $table) {
            $table->dropColumn([
                'og_title',
                'og_description',
                'og_image_media_id',
                'og_image_width',
                'og_image_height',
            ]);
        });
    }
};
