<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update atau create user teknisi
        User::updateOrCreate(
            ['email' => 'teknisi@jti.com'],
            [
                'name' => 'Admin Teknisi',
                'password' => Hash::make('password123'),
            ]
        );

        // Update atau create user test
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );
    }
}