<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@rentease.com',
            'password' => Hash::make('admin123'),
            'full_name' => 'System Administrator',
            'user_type' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'username' => 'landlord1',
            'email' => 'landlord@rentease.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Juan Dela Cruz',
            'phone' => '09123456789',
            'user_type' => 'landlord',
            'email_verified_at' => now(),
        ]);

        User::create([
            'username' => 'tenant1',
            'email' => 'tenant@rentease.com',
            'password' => Hash::make('password123'),
            'full_name' => 'Maria Santos',
            'phone' => '09987654321',
            'user_type' => 'tenant',
            'email_verified_at' => now(),
        ]);
    }
}
