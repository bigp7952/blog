<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@sunublog.com',
            'phone' => '+33123456789',
            'password' => Hash::make('password'),
            'bio' => 'Administrateur de SunuBlog',
            'avatar' => '/api/placeholder/64/64',
            'is_online' => true,
            'last_seen' => now(),
        ]);

        // Create test users
        $users = [
            [
                'name' => 'Alice Martin',
                'username' => 'alice_dev',
                'email' => 'alice@example.com',
                'phone' => '+33123456790',
                'password' => Hash::make('password'),
                'bio' => 'Développeuse Frontend passionnée par React et TypeScript',
                'avatar' => '/api/placeholder/64/64',
                'is_online' => true,
                'last_seen' => now(),
            ],
            [
                'name' => 'Bob Johnson',
                'username' => 'bob_writer',
                'email' => 'bob@example.com',
                'phone' => '+33123456791',
                'password' => Hash::make('password'),
                'bio' => 'Tech Lead & Bloggeur technique',
                'avatar' => '/api/placeholder/64/64',
                'is_online' => false,
                'last_seen' => now()->subHours(2),
            ],
            [
                'name' => 'Charlie Brown',
                'username' => 'charlie_design',
                'email' => 'charlie@example.com',
                'phone' => '+33123456792',
                'password' => Hash::make('password'),
                'bio' => 'Designer UI/UX créatif',
                'avatar' => '/api/placeholder/64/64',
                'is_online' => true,
                'last_seen' => now(),
            ],
            [
                'name' => 'Diana Prince',
                'username' => 'diana_backend',
                'email' => 'diana@example.com',
                'phone' => '+33123456793',
                'password' => Hash::make('password'),
                'bio' => 'Développeuse Backend spécialisée en Laravel',
                'avatar' => '/api/placeholder/64/64',
                'is_online' => false,
                'last_seen' => now()->subDays(1),
            ],
            [
                'name' => 'Eve Wilson',
                'username' => 'eve_mobile',
                'email' => 'eve@example.com',
                'phone' => '+33123456794',
                'password' => Hash::make('password'),
                'bio' => 'Développeuse Mobile React Native',
                'avatar' => '/api/placeholder/64/64',
                'is_online' => true,
                'last_seen' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
