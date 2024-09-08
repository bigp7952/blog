<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;
use App\Models\Tag;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $tags = Tag::all();

        $articles = [
            [
                'title' => 'Les Tendances du Développement Web en 2025',
                'excerpt' => 'Découvrez les technologies qui vont révolutionner le développement web cette année. De l\'IA générative aux frameworks modernes, explorez l\'avenir du développement.',
                'content' => 'Le développement web évolue rapidement et 2025 promet d\'être une année riche en innovations. Dans cet article, nous explorons les tendances qui vont façonner l\'avenir du développement web.

## 1. L\'Intelligence Artificielle Générative

L\'IA générative transforme la façon dont nous développons des applications web. Des outils comme GitHub Copilot et ChatGPT révolutionnent l\'écriture de code, permettant aux développeurs de créer plus rapidement et efficacement.

## 2. Les Frameworks Modernes

React 18, Vue 3, et Angular 17 continuent d\'évoluer avec de nouvelles fonctionnalités qui améliorent les performances et l\'expérience développeur.

## 3. Le Développement Full-Stack

Les développeurs full-stack sont de plus en plus recherchés, maîtrisant à la fois le frontend et le backend pour créer des applications complètes.

## Conclusion

Le développement web en 2025 sera marqué par l\'innovation, l\'efficacité et la créativité. Restez à jour avec ces tendances pour rester compétitif dans ce domaine en constante évolution.',
                'status' => 'published',
                'visibility' => 'public',
                'featured' => true,
                'views' => 1234,
                'likes_count' => 89,
                'comments_count' => 18,
                'bookmarks_count' => 45,
                'published_at' => now()->subDays(1),
                'tags' => ['Web Dev', 'Tendances', '2025', 'React'],
            ],
            [
                'title' => 'Guide Complet de React 18 : Nouvelles Fonctionnalités',
                'excerpt' => 'Maîtrisez les nouvelles fonctionnalités de React 18 avec des exemples pratiques et des cas d\'usage concrets.',
                'content' => 'React 18 apporte de nombreuses améliorations et nouvelles fonctionnalités qui changent la façon dont nous développons des applications React.

## Les Nouvelles Fonctionnalités

### 1. Concurrent Rendering
Le rendu concurrent permet à React d\'interrompre le rendu pour traiter des mises à jour plus importantes.

### 2. Automatic Batching
React 18 améliore le batching automatique pour de meilleures performances.

### 3. Suspense Amélioré
Le composant Suspense est maintenant plus puissant et flexible.

## Exemples Pratiques

Voici comment utiliser ces nouvelles fonctionnalités dans vos projets React.',
                'status' => 'published',
                'visibility' => 'public',
                'featured' => false,
                'views' => 2156,
                'likes_count' => 156,
                'comments_count' => 32,
                'bookmarks_count' => 78,
                'published_at' => now()->subDays(2),
                'tags' => ['React', 'JavaScript', 'Tutorial', 'Frontend'],
            ],
            [
                'title' => 'L\'Art de la Rédaction Technique : Conseils pour Écrire des Articles de Qualité',
                'excerpt' => 'Apprenez les techniques essentielles pour rédiger des articles techniques engageants et informatifs qui captent l\'attention de votre audience.',
                'content' => 'La rédaction technique est un art qui nécessite de la pratique et de la méthode. Voici nos conseils pour créer du contenu de qualité.

## Structure de l\'Article

### 1. Introduction Captivante
Commencez par une introduction qui pose le problème et annonce la solution.

### 2. Développement Structuré
Organisez votre contenu en sections logiques avec des sous-titres clairs.

### 3. Conclusion Actionnable
Terminez par une conclusion qui résume les points clés et propose des actions concrètes.

## Conseils d\'Écriture

- Utilisez un langage clair et accessible
- Illustrez vos propos avec des exemples concrets
- Structurez votre contenu avec des listes et des tableaux
- Relisez et corrigez votre texte avant publication',
                'status' => 'published',
                'visibility' => 'public',
                'featured' => false,
                'views' => 987,
                'likes_count' => 67,
                'comments_count' => 12,
                'bookmarks_count' => 34,
                'published_at' => now()->subDays(3),
                'tags' => ['Tutorial', 'Écriture', 'Conseils'],
            ],
            [
                'title' => 'TypeScript vs JavaScript : Quand Utiliser Chacun ?',
                'excerpt' => 'Comparaison détaillée entre TypeScript et JavaScript pour vous aider à choisir la meilleure solution pour votre projet.',
                'content' => 'TypeScript et JavaScript sont deux technologies complémentaires mais distinctes. Voici comment choisir entre elles.

## TypeScript : Les Avantages

### Typage Statique
TypeScript offre un système de types qui aide à détecter les erreurs avant l\'exécution.

### Meilleure IntelliSense
L\'autocomplétion et la documentation intégrée améliorent l\'expérience développeur.

### Refactoring Sécurisé
Les types facilitent la refactorisation du code en toute sécurité.

## JavaScript : Quand l\'Utiliser

### Prototypage Rapide
JavaScript reste idéal pour les prototypes et les projets rapides.

### Écosystème Vaste
L\'écosystème JavaScript est immense et bien établi.

## Conclusion

Choisissez TypeScript pour les projets complexes et JavaScript pour les projets simples ou rapides.',
                'status' => 'published',
                'visibility' => 'public',
                'featured' => false,
                'views' => 1456,
                'likes_count' => 98,
                'comments_count' => 25,
                'bookmarks_count' => 56,
                'published_at' => now()->subDays(4),
                'tags' => ['TypeScript', 'JavaScript', 'Comparaison', 'Tutorial'],
            ],
            [
                'title' => 'Optimisation des Performances Web : Techniques Avancées',
                'excerpt' => 'Découvrez les techniques avancées pour optimiser les performances de vos applications web et améliorer l\'expérience utilisateur.',
                'content' => 'L\'optimisation des performances web est cruciale pour offrir une excellente expérience utilisateur.

## Techniques d\'Optimisation

### 1. Lazy Loading
Chargez les ressources seulement quand elles sont nécessaires.

### 2. Code Splitting
Divisez votre code en chunks plus petits pour un chargement plus rapide.

### 3. Optimisation des Images
Utilisez des formats modernes comme WebP et optimisez la taille des images.

### 4. Mise en Cache
Implémentez des stratégies de cache efficaces pour réduire les requêtes serveur.

## Outils de Mesure

- Lighthouse pour l\'audit des performances
- WebPageTest pour des tests détaillés
- Chrome DevTools pour le debugging

## Conclusion

L\'optimisation des performances est un processus continu qui nécessite une attention constante.',
                'status' => 'published',
                'visibility' => 'public',
                'featured' => false,
                'views' => 876,
                'likes_count' => 54,
                'comments_count' => 8,
                'bookmarks_count' => 23,
                'published_at' => now()->subDays(5),
                'tags' => ['Performance', 'Optimisation', 'Web Dev', 'Tutorial'],
            ],
        ];

        foreach ($articles as $index => $articleData) {
            $user = $users->random();
            $tags = $articleData['tags'];
            unset($articleData['tags']);

            $article = Article::create([
                'user_id' => $user->id,
                'title' => $articleData['title'],
                'slug' => \Illuminate\Support\Str::slug($articleData['title']),
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'status' => $articleData['status'],
                'visibility' => $articleData['visibility'],
                'featured' => $articleData['featured'],
                'views' => $articleData['views'],
                'likes_count' => $articleData['likes_count'],
                'comments_count' => $articleData['comments_count'],
                'bookmarks_count' => $articleData['bookmarks_count'],
                'published_at' => $articleData['published_at'],
            ]);

            // Attach tags
            $tagIds = Tag::whereIn('name', $tags)->pluck('id');
            $article->tags()->attach($tagIds);
        }
    }
}
