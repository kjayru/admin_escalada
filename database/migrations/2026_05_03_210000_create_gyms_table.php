<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedBigInteger('logo_media_id')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('logo_media_id')
                  ->references('id')
                  ->on('media_files')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};
