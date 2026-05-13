<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed con las categorías existentes
        DB::table('blog_categories')->insert([
            ['name' => 'Blog',      'slug' => 'blog',     'description' => null, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Eventos',   'slug' => 'eventos',  'description' => null, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Noticias',  'slug' => 'noticias', 'description' => null, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
