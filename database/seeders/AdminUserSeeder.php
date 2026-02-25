<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@escalada.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('r3d3nc10n'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
