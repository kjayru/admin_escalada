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
        Schema::create('sponsor_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsor_id')->constrained()->cascadeOnDelete();
            $table->string('placement'); // home_footer, blog_sidebar, global
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->foreignId('banner_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('link_url')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsor_placements');
    }
};
