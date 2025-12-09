<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@test.com',
            'phone' => '081234567890',
            'password_hash' => Hash::make('password'),
            'balance' => 1000000,
            'is_admin' => true,
        ]);

        // Create regular test user
        User::create([
            'username' => 'testuser',
            'email' => 'user@test.com',
            'phone' => '081234567891',
            'password_hash' => Hash::make('password'),
            'balance' => 500000,
            'is_admin' => false,
        ]);
    }
}
