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
        Schema::create('support_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('support_campaigns')->cascadeOnDelete();
            $table->string('type'); // paypal, transfer, gym, product
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('settings')->nullable(); // cuentas, emails, links
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
        Schema::dropIfExists('support_methods');
    }
};
