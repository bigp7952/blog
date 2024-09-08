<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Web Dev',
                'slug' => 'web-dev',
                'description' => 'Développement web et technologies frontend/backend',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'React',
                'slug' => 'react',
                'description' => 'Bibliothèque JavaScript pour créer des interfaces utilisateur',
                'color' => '#61DAFB',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'description' => 'Framework PHP pour le développement web',
                'color' => '#FF2D20',
            ],
            [
                'name' => 'TypeScript',
                'slug' => 'typescript',
                'description' => 'JavaScript avec typage statique',
                'color' => '#3178C6',
            ],
            [
                'name' => 'Tendances',
                'slug' => 'tendances',
                'description' => 'Tendances et nouveautés technologiques',
                'color' => '#10B981',
            ],
            [
                'name' => 'Tutorial',
                'slug' => 'tutorial',
                'description' => 'Tutoriels et guides pratiques',
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Frontend',
                'slug' => 'frontend',
                'description' => 'Développement frontend et interfaces utilisateur',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Backend',
                'slug' => 'backend',
                'description' => 'Développement backend et logique métier',
                'color' => '#EF4444',
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'Langage de programmation JavaScript',
                'color' => '#F7DF1E',
            ],
            [
                'name' => 'PHP',
                'slug' => 'php',
                'description' => 'Langage de programmation PHP',
                'color' => '#777BB4',
            ],
            [
                'name' => 'CSS',
                'slug' => 'css',
                'description' => 'Feuilles de style en cascade',
                'color' => '#1572B6',
            ],
            [
                'name' => 'HTML',
                'slug' => 'html',
                'description' => 'Langage de balisage hypertexte',
                'color' => '#E34F26',
            ],
            [
                'name' => 'Node.js',
                'slug' => 'nodejs',
                'description' => 'Runtime JavaScript côté serveur',
                'color' => '#339933',
            ],
            [
                'name' => 'Vue.js',
                'slug' => 'vuejs',
                'description' => 'Framework JavaScript progressif',
                'color' => '#4FC08D',
            ],
            [
                'name' => 'Angular',
                'slug' => 'angular',
                'description' => 'Framework JavaScript pour applications web',
                'color' => '#DD0031',
            ],
            [
                'name' => 'Python',
                'slug' => 'python',
                'description' => 'Langage de programmation Python',
                'color' => '#3776AB',
            ],
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'description' => 'Plateforme de conteneurisation',
                'color' => '#2496ED',
            ],
            [
                'name' => 'Git',
                'slug' => 'git',
                'description' => 'Système de contrôle de version',
                'color' => '#F05032',
            ],
            [
                'name' => 'API',
                'slug' => 'api',
                'description' => 'Interface de programmation d\'application',
                'color' => '#FF6B6B',
            ],
            [
                'name' => 'Base de données',
                'slug' => 'base-de-donnees',
                'description' => 'Gestion et manipulation de données',
                'color' => '#4ECDC4',
            ],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }
    }
}
