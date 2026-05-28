<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar configuración para el asunto del email de contacto desde blog
        DB::table('site_settings')->insert([
            [
                'key' => 'email_subject',
                'value' => 'Contacto desde Blog - Escalada PRO',
                'group' => 'contact',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('site_settings')
            ->where('key', 'email_subject')
            ->delete();
    }
};
