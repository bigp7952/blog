<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $tags = Tag::all();

        if ($users->isEmpty() || $tags->isEmpty()) {
            return;
        }

        $articles = [
            [
                'title' => 'Guide Complet de React 18 : Nouvelles Fonctionnalités',
                'excerpt' => 'Découvrez les nouvelles fonctionnalités de React 18, incluant le rendu concurrent, les Suspense améliorés et les hooks optimisés.',
                'content' => 'React 18 apporte de nombreuses améliorations significatives qui révolutionnent la façon dont nous construisons des applications React modernes.

## Rendu Concurrent

Le rendu concurrent est l\'une des fonctionnalités les plus importantes de React 18. Il permet à React de préparer plusieurs versions de l\'interface utilisateur en même temps, rendant les applications plus réactives.

### Avantages du rendu concurrent :
- **Interruption du rendu** : React peut interrompre le rendu pour traiter des mises à jour plus prioritaires
- **Amélioration de la fluidité** : Les interactions utilisateur restent fluides même pendant les mises à jour
- **Meilleure gestion des états** : Évite les états incohérents

## Suspense Amélioré

React 18 améliore considérablement le composant Suspense, permettant de l\'utiliser avec le rendu côté serveur (SSR).

## Hooks Optimisés

Plusieurs hooks ont été optimisés pour de meilleures performances :
- `useId` : Génère des IDs uniques
- `useDeferredValue` : Retarde les mises à jour non critiques
- `useTransition` : Marque les mises à jour comme non urgentes

## Conclusion

React 18 représente une étape majeure dans l\'évolution de React, offrant de meilleures performances et une expérience développeur améliorée.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subDays(5),
                'tags' => ['React', 'JavaScript', 'Tutorial', 'Frontend']
            ],
            [
                'title' => 'Laravel 10 : Les Meilleures Pratiques pour un Code Propre',
                'excerpt' => 'Explorez les meilleures pratiques pour écrire du code Laravel propre, maintenable et performant.',
                'content' => 'Laravel 10 continue d\'évoluer avec de nouvelles fonctionnalités et améliorations. Voici les meilleures pratiques pour tirer le meilleur parti de ce framework.

## Architecture MVC Respectée

Respectez toujours l\'architecture MVC de Laravel. Cette approche garantit une séparation claire des responsabilités et facilite la maintenance du code.

### Modèles (Models)
Les modèles représentent vos données et contiennent la logique métier. Utilisez les relations Eloquent pour simplifier vos requêtes.

### Contrôleurs (Controllers)
Les contrôleurs gèrent la logique de présentation et coordonnent entre les modèles et les vues.

## Utilisation des Form Requests

Utilisez les Form Requests pour la validation. Cette approche centralise la logique de validation et améliore la lisibilité du code.

## Gestion des Relations

Optimisez vos requêtes avec les relations :
- Évitez le problème N+1 avec `with()`
- Utilisez les requêtes optimisées avec `withCount()`

## Conclusion

Ces pratiques vous aideront à maintenir un code Laravel propre et performant.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subDays(3),
                'tags' => ['Laravel', 'PHP', 'Best Practices', 'Backend']
            ],
            [
                'title' => 'TypeScript vs JavaScript : Quand Faire la Transition ?',
                'excerpt' => 'Analyse comparative entre TypeScript et JavaScript pour vous aider à décider quand migrer vos projets.',
                'content' => 'La question de migrer de JavaScript vers TypeScript se pose souvent dans les projets modernes. Voici une analyse complète pour vous aider à prendre la bonne décision.

## Avantages de TypeScript

### Typage Statique
TypeScript apporte le typage statique à JavaScript, réduisant considérablement les erreurs de type.

### Meilleure IntelliSense
L\'autocomplétion et la détection d\'erreurs sont considérablement améliorées.

### Refactoring Sécurisé
Le refactoring devient plus sûr avec TypeScript.

## Inconvénients de TypeScript

### Courbe d\'Apprentissage
- Syntaxe plus complexe
- Concepts de génériques et d\'interfaces
- Configuration initiale

### Taille du Bundle
- Code JavaScript généré plus volumineux
- Temps de compilation

## Quand Migrer vers TypeScript ?

### Migrez si :
- Votre équipe est prête à apprendre
- Le projet est de taille moyenne à grande
- La maintenance à long terme est importante
- Vous travaillez en équipe

### Restez en JavaScript si :
- Projet simple et rapide
- Équipe non familière avec TypeScript
- Contraintes de temps strictes

## Conclusion

TypeScript est un excellent choix pour la plupart des projets modernes, mais la décision doit être prise en fonction de votre contexte spécifique.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subDays(1),
                'tags' => ['TypeScript', 'JavaScript', 'Tutorial', 'Frontend']
            ],
            [
                'title' => 'Optimisation des Performances Web : Techniques Avancées',
                'excerpt' => 'Découvrez les techniques avancées pour optimiser les performances de vos applications web.',
                'content' => 'L\'optimisation des performances web est cruciale pour offrir une excellente expérience utilisateur. Voici les techniques avancées que vous devez connaître.

## Optimisation des Images

### Formats Modernes
Utilisez des formats d\'image modernes comme WebP et AVIF pour réduire la taille des fichiers.

### Lazy Loading
Implémentez le lazy loading pour charger les images uniquement quand elles sont nécessaires.

## Optimisation JavaScript

### Code Splitting
Divisez votre code JavaScript en chunks plus petits pour améliorer les temps de chargement.

### Tree Shaking
Éliminez le code mort de votre bundle final.

## Optimisation CSS

### Critical CSS
Incluez le CSS critique inline pour améliorer le First Contentful Paint.

### CSS Purging
Supprimez le CSS non utilisé de votre build final.

## Optimisation des Requêtes

### Requêtes Optimisées
Utilisez les relations Eloquent pour éviter les requêtes N+1.

### Mise en Cache
Implémentez la mise en cache pour les requêtes coûteuses.

## Conclusion

L\'optimisation des performances est un processus continu qui nécessite une approche holistique.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subHours(12),
                'tags' => ['Performance', 'Optimisation', 'Web Dev', 'Best Practices']
            ],
            [
                'title' => 'Introduction à l\'Intelligence Artificielle pour les Développeurs',
                'excerpt' => 'Découvrez comment intégrer l\'IA dans vos applications web modernes avec des exemples pratiques.',
                'content' => 'L\'Intelligence Artificielle transforme le développement web. Voici comment intégrer l\'IA dans vos applications.

## Types d\'IA pour le Web

### Machine Learning
Le machine learning permet aux applications d\'apprendre et de s\'améliorer automatiquement.

### Deep Learning
Le deep learning utilise des réseaux de neurones pour résoudre des problèmes complexes.

## Intégration dans les Applications Web

### API REST pour l\'IA
Créez des endpoints API pour exposer vos modèles d\'IA.

### Frontend avec IA
Intégrez des modèles d\'IA directement dans le navigateur avec TensorFlow.js.

## Cas d\'Usage Pratiques

### 1. Recommandations
Implémentez des systèmes de recommandation personnalisés.

### 2. Analyse de Sentiment
Analysez le sentiment des commentaires et avis utilisateurs.

### 3. Chatbot Intelligent
Créez des chatbots capables de comprendre et répondre aux utilisateurs.

## Outils et Frameworks

### Backend
- **Python** : TensorFlow, PyTorch, scikit-learn
- **Node.js** : TensorFlow.js, Brain.js
- **PHP** : PHP-ML, Rubix ML

### Frontend
- **TensorFlow.js** : IA côté client
- **ML5.js** : Bibliothèque simplifiée
- **OpenAI API** : Intégration GPT

## Conclusion

L\'intégration de l\'IA dans les applications web ouvre de nouvelles possibilités. Commencez par des cas d\'usage simples et évoluez progressivement.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subHours(6),
                'tags' => ['AI', 'Machine Learning', 'Python', 'JavaScript']
            ],
            [
                'title' => 'Sécurité Web : Protéger vos Applications des Vulnérabilités',
                'excerpt' => 'Guide complet sur la sécurité web : authentification, autorisation, et protection contre les attaques courantes.',
                'content' => 'La sécurité web est un aspect crucial du développement moderne. Voici un guide complet pour protéger vos applications.

## Authentification Sécurisée

### JWT avec Refresh Tokens
Implémentez un système d\'authentification robuste avec des tokens JWT et des refresh tokens.

### Hachage des Mots de Passe
Utilisez des algorithmes de hachage sécurisés comme bcrypt.

## Protection CSRF

### Laravel
Laravel inclut une protection CSRF automatique avec ses middlewares.

### React
Implémentez la protection CSRF côté client avec des tokens.

## Protection XSS

### Échappement des Données
Échappez toujours les données utilisateur avant de les afficher.

### Validation et Nettoyage
Validez et nettoyez toutes les entrées utilisateur.

## Protection SQL Injection

### Requêtes Préparées
Utilisez toujours des requêtes préparées ou l\'ORM.

### Validation des Entrées
Validez strictement toutes les entrées utilisateur.

## Headers de Sécurité

Configurez les headers de sécurité appropriés pour protéger votre application.

## Rate Limiting

Implémentez la limitation du taux de requêtes pour prévenir les attaques par déni de service.

## Audit de Sécurité

### Outils d\'Audit
Utilisez des outils comme OWASP ZAP pour auditer votre application.

### Tests de Sécurité
Implémentez des tests automatisés pour vérifier la sécurité.

## Bonnes Pratiques

### 1. Principe du Moindre Privilège
Accordez uniquement les permissions nécessaires.

### 2. Logging et Monitoring
Surveillez les événements de sécurité.

### 3. Mise à Jour Régulière
Maintenez vos dépendances à jour.

## Conclusion

La sécurité web nécessite une approche multicouche. Implémentez ces mesures progressivement et restez informé des dernières menaces.',
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subHours(2),
                'tags' => ['Sécurité', 'Authentication', 'Best Practices', 'Web Dev']
            ]
        ];

        foreach ($articles as $articleData) {
            $user = $users->random();
            $articleTags = $articleData['tags'];
            unset($articleData['tags']);

            $article = Article::create([
                ...$articleData,
                'user_id' => $user->id,
                'slug' => Str::slug($articleData['title']),
            ]);

            // Attacher les tags
            $tagIds = $tags->whereIn('name', $articleTags)->pluck('id');
            $article->tags()->attach($tagIds);

            // Mettre à jour les compteurs
            $article->update([
                'views' => rand(50, 2000),
                'likes_count' => rand(10, 500),
                'comments_count' => rand(0, 100),
                'bookmarks_count' => rand(5, 200),
            ]);
        }
    }
}