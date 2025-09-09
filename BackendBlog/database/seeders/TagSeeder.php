<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'React', 'slug' => 'react', 'description' => 'Bibliothèque JavaScript pour créer des interfaces utilisateur'],
            ['name' => 'Laravel', 'slug' => 'laravel', 'description' => 'Framework PHP pour le développement web'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'Langage de programmation web'],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'description' => 'JavaScript avec typage statique'],
            ['name' => 'PHP', 'slug' => 'php', 'description' => 'Langage de programmation côté serveur'],
            ['name' => 'Vue.js', 'slug' => 'vue-js', 'description' => 'Framework JavaScript progressif'],
            ['name' => 'Node.js', 'slug' => 'node-js', 'description' => 'Runtime JavaScript côté serveur'],
            ['name' => 'MySQL', 'slug' => 'mysql', 'description' => 'Système de gestion de base de données'],
            ['name' => 'MongoDB', 'slug' => 'mongodb', 'description' => 'Base de données NoSQL'],
            ['name' => 'Docker', 'slug' => 'docker', 'description' => 'Plateforme de conteneurisation'],
            ['name' => 'AWS', 'slug' => 'aws', 'description' => 'Services cloud Amazon Web Services'],
            ['name' => 'Git', 'slug' => 'git', 'description' => 'Système de contrôle de version'],
            ['name' => 'API', 'slug' => 'api', 'description' => 'Interface de programmation d\'application'],
            ['name' => 'REST', 'slug' => 'rest', 'description' => 'Architecture de services web'],
            ['name' => 'GraphQL', 'slug' => 'graphql', 'description' => 'Langage de requête pour API'],
            ['name' => 'CSS', 'slug' => 'css', 'description' => 'Langage de style pour le web'],
            ['name' => 'HTML', 'slug' => 'html', 'description' => 'Langage de balisage hypertexte'],
            ['name' => 'Sass', 'slug' => 'sass', 'description' => 'Préprocesseur CSS'],
            ['name' => 'TailwindCSS', 'slug' => 'tailwindcss', 'description' => 'Framework CSS utility-first'],
            ['name' => 'Bootstrap', 'slug' => 'bootstrap', 'description' => 'Framework CSS responsive'],
            ['name' => 'Webpack', 'slug' => 'webpack', 'description' => 'Module bundler JavaScript'],
            ['name' => 'Vite', 'slug' => 'vite', 'description' => 'Outil de build rapide'],
            ['name' => 'Jest', 'slug' => 'jest', 'description' => 'Framework de test JavaScript'],
            ['name' => 'Cypress', 'slug' => 'cypress', 'description' => 'Framework de test end-to-end'],
            ['name' => 'Tutorial', 'slug' => 'tutorial', 'description' => 'Guide d\'apprentissage'],
            ['name' => 'Best Practices', 'slug' => 'best-practices', 'description' => 'Meilleures pratiques de développement'],
            ['name' => 'Performance', 'slug' => 'performance', 'description' => 'Optimisation des performances'],
            ['name' => 'Sécurité', 'slug' => 'securite', 'description' => 'Sécurité informatique'],
            ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'Pratiques de développement et opérations'],
            ['name' => 'Mobile', 'slug' => 'mobile', 'description' => 'Développement mobile'],
            ['name' => 'React Native', 'slug' => 'react-native', 'description' => 'Framework mobile React'],
            ['name' => 'Flutter', 'slug' => 'flutter', 'description' => 'Framework mobile Google'],
            ['name' => 'Python', 'slug' => 'python', 'description' => 'Langage de programmation polyvalent'],
            ['name' => 'Django', 'slug' => 'django', 'description' => 'Framework web Python'],
            ['name' => 'FastAPI', 'slug' => 'fastapi', 'description' => 'Framework web Python moderne'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'description' => 'Base de données relationnelle avancée'],
            ['name' => 'Redis', 'slug' => 'redis', 'description' => 'Base de données en mémoire'],
            ['name' => 'Elasticsearch', 'slug' => 'elasticsearch', 'description' => 'Moteur de recherche et d\'analyse'],
            ['name' => 'Kubernetes', 'slug' => 'kubernetes', 'description' => 'Orchestrateur de conteneurs'],
            ['name' => 'Terraform', 'slug' => 'terraform', 'description' => 'Infrastructure as Code'],
            ['name' => 'CI/CD', 'slug' => 'ci-cd', 'description' => 'Intégration et déploiement continus'],
            ['name' => 'Microservices', 'slug' => 'microservices', 'description' => 'Architecture de microservices'],
            ['name' => 'Serverless', 'slug' => 'serverless', 'description' => 'Architecture sans serveur'],
            ['name' => 'Blockchain', 'slug' => 'blockchain', 'description' => 'Technologie de chaîne de blocs'],
            ['name' => 'Cryptocurrency', 'slug' => 'cryptocurrency', 'description' => 'Cryptomonnaies'],
            ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'description' => 'Apprentissage automatique'],
            ['name' => 'Deep Learning', 'slug' => 'deep-learning', 'description' => 'Apprentissage profond'],
            ['name' => 'TensorFlow', 'slug' => 'tensorflow', 'description' => 'Framework de machine learning'],
            ['name' => 'PyTorch', 'slug' => 'pytorch', 'description' => 'Framework de deep learning'],
            ['name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Science des données'],
            ['name' => 'Analytics', 'slug' => 'analytics', 'description' => 'Analyse de données'],
            ['name' => 'Big Data', 'slug' => 'big-data', 'description' => 'Traitement de grandes données']
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }
    }
}

