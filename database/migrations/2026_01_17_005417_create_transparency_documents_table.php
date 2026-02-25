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
        Schema::create('transparency_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('year');
            $table->string('type'); // financial, report, legal, other
            $table->text('description')->nullable();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transparency_documents');
    }
};
