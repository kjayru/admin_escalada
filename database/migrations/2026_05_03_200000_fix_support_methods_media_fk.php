<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_methods', function (Blueprint $table) {
            // Drop FK to legacy_media and replace with FK to media_files (slimani)
            $table->dropForeign('support_methods_media_id_foreign');
            $table->foreign('media_id')->references('id')->on('media_files')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('support_methods', function (Blueprint $table) {
            $table->dropForeign(['media_id']);
            $table->foreign('media_id')->references('id')->on('legacy_media')->nullOnDelete();
        });
    }
};
