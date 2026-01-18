<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@email.com',
            'password' => Hash::make('admin@123123123'),
            'role' => 'admin',
        ]);

        UserPreference::create([
            'user_id' => $admin->id,
            'theme' => 'light',
            'notifications_enabled' => true,
        ]);

        // Create test user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        UserPreference::create([
            'user_id' => $user->id,
            'theme' => 'light',
            'notifications_enabled' => true,
        ]);
    }
}
