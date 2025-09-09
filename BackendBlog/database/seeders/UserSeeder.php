<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Aminata Diop',
                'username' => 'aminata_dev',
                'email' => 'aminata.diop@example.com',
                'phone' => '+221701234567',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Développeuse Full-Stack passionnée par React et Laravel. Originaire de Dakar.',
                'is_online' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Moussa Fall',
                'username' => 'moussa_writer',
                'email' => 'moussa.fall@example.com',
                'phone' => '+221701234568',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Tech Lead et blogueur technique. Spécialisé en architecture logicielle.',
                'is_online' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Fatou Sarr',
                'username' => 'fatou_ux',
                'email' => 'fatou.sarr@example.com',
                'phone' => '+221701234569',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'UX/UI Designer créative. Passionnée par l\'expérience utilisateur et le design thinking.',
                'is_online' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ibrahima Ndiaye',
                'username' => 'ibrahima_backend',
                'email' => 'ibrahima.ndiaye@example.com',
                'phone' => '+221701234570',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Développeur Backend spécialisé en Laravel et Node.js. Expert en bases de données.',
                'is_online' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Aïcha Ba',
                'username' => 'aicha_mobile',
                'email' => 'aicha.ba@example.com',
                'phone' => '+221701234571',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Développeuse Mobile React Native. Créatrice d\'applications innovantes.',
                'is_online' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Cheikh Thiam',
                'username' => 'cheikh_ai',
                'email' => 'cheikh.thiam@example.com',
                'phone' => '+221701234572',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Data Scientist et expert en Intelligence Artificielle. Passionné par l\'innovation technologique.',
                'is_online' => false,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Mariama Diallo',
                'username' => 'mariama_frontend',
                'email' => 'mariama.diallo@example.com',
                'phone' => '+221701234573',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'Développeuse Frontend spécialisée en React et Vue.js. Créatrice d\'interfaces modernes.',
                'is_online' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ousmane Gueye',
                'username' => 'ousmane_devops',
                'email' => 'ousmane.gueye@example.com',
                'phone' => '+221701234574',
                'password' => Hash::make('password123'),
                'avatar' => null,
                'bio' => 'DevOps Engineer. Expert en déploiement et infrastructure cloud.',
                'is_online' => false,
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}

