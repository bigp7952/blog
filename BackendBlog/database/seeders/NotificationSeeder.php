<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Article;
use App\Models\Comment;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $articles = Article::all();
        $comments = Comment::all();

        if ($users->isEmpty() || $articles->isEmpty()) {
            return;
        }

        $notifications = [];

        // Notifications de nouveaux commentaires
        foreach ($comments->take(10) as $comment) {
            $article = $articles->find($comment->article_id);
            if ($article && $comment->user_id !== $article->user_id) {
                $notifications[] = [
                    'user_id' => $article->user_id,
                    'type' => 'comment',
                    'title' => 'Nouveau commentaire',
                    'message' => $comment->user->name . ' a commenté votre article "' . $article->title . '"',
                    'data' => json_encode([
                        'article_id' => $article->id,
                        'comment_id' => $comment->id,
                        'commenter_id' => $comment->user_id
                    ]),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 24)) : null,
                    'created_at' => $comment->created_at,
                ];
            }
        }

        // Notifications de demandes d'amitié
        $friendRequests = [
            ['user_id' => 1, 'requester_id' => 6, 'requester_name' => 'Cheikh Thiam'],
            ['user_id' => 2, 'requester_id' => 7, 'requester_name' => 'Mariama Diallo'],
            ['user_id' => 3, 'requester_id' => 8, 'requester_name' => 'Ousmane Gueye'],
            ['user_id' => 4, 'requester_id' => 6, 'requester_name' => 'Cheikh Thiam'],
            ['user_id' => 5, 'requester_id' => 7, 'requester_name' => 'Mariama Diallo'],
        ];

        foreach ($friendRequests as $request) {
            if ($users->find($request['user_id']) && $users->find($request['requester_id'])) {
                $notifications[] = [
                    'user_id' => $request['user_id'],
                    'type' => 'friend_request',
                    'title' => 'Nouvelle demande d\'ami',
                    'message' => $request['requester_name'] . ' souhaite vous ajouter comme ami',
                    'data' => json_encode([
                        'requester_id' => $request['requester_id']
                    ]),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                    'created_at' => now()->subDays(rand(1, 7)),
                ];
            }
        }

        // Notifications d'articles populaires
        foreach ($articles->take(3) as $article) {
            if ($article->views > 1000) {
                $notifications[] = [
                    'user_id' => $article->user_id,
                    'type' => 'article_popular',
                    'title' => 'Article populaire !',
                    'message' => 'Votre article "' . $article->title . '" a atteint ' . $article->views . ' vues !',
                    'data' => json_encode([
                        'article_id' => $article->id,
                        'views' => $article->views
                    ]),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 12)) : null,
                    'created_at' => now()->subDays(rand(1, 3)),
                ];
            }
        }

        // Notifications de likes sur les articles
        $likedArticles = $articles->take(5);
        foreach ($likedArticles as $article) {
            $randomUser = $users->where('id', '!=', $article->user_id)->random();
            $notifications[] = [
                'user_id' => $article->user_id,
                'type' => 'article_liked',
                'title' => 'Article aimé',
                'message' => $randomUser->name . ' a aimé votre article "' . $article->title . '"',
                'data' => json_encode([
                    'article_id' => $article->id,
                    'liker_id' => $randomUser->id
                ]),
                'read_at' => rand(0, 1) ? now()->subHours(rand(1, 6)) : null,
                'created_at' => now()->subHours(rand(1, 12)),
            ];
        }

        // Notifications de bookmarks
        foreach ($articles->take(3) as $article) {
            $randomUser = $users->where('id', '!=', $article->user_id)->random();
            $notifications[] = [
                'user_id' => $article->user_id,
                'type' => 'article_bookmarked',
                'title' => 'Article sauvegardé',
                'message' => $randomUser->name . ' a sauvegardé votre article "' . $article->title . '"',
                'data' => json_encode([
                    'article_id' => $article->id,
                    'bookmarker_id' => $randomUser->id
                ]),
                'read_at' => rand(0, 1) ? now()->subHours(rand(1, 8)) : null,
                'created_at' => now()->subHours(rand(1, 16)),
            ];
        }

        // Notifications système
        $systemNotifications = [
            [
                'user_id' => 1,
                'type' => 'system',
                'title' => 'Bienvenue sur SunuBlog !',
                'message' => 'Merci de vous être inscrit. Explorez les articles et connectez-vous avec d\'autres développeurs.',
                'data' => json_encode(['type' => 'welcome']),
                'read_at' => now()->subDays(5),
                'created_at' => now()->subDays(5),
            ],
            [
                'user_id' => 2,
                'type' => 'system',
                'title' => 'Nouvelle fonctionnalité disponible',
                'message' => 'Vous pouvez maintenant créer des articles avec des tags et des images.',
                'data' => json_encode(['type' => 'feature_update']),
                'read_at' => null,
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => 3,
                'type' => 'system',
                'title' => 'Maintenance programmée',
                'message' => 'Une maintenance est prévue dimanche de 2h à 4h. Le service pourrait être temporairement indisponible.',
                'data' => json_encode(['type' => 'maintenance']),
                'read_at' => null,
                'created_at' => now()->subHours(6),
            ]
        ];

        $notifications = array_merge($notifications, $systemNotifications);

        // Insérer toutes les notifications
        foreach ($notifications as $notificationData) {
            Notification::create($notificationData);
        }
    }
}

