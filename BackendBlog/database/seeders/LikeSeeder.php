<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Like;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

class LikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $articles = Article::all();
        $comments = Comment::all();

        if ($users->isEmpty() || $articles->isEmpty()) {
            return;
        }

        // Likes sur les articles
        foreach ($articles as $article) {
            // Chaque article reçoit entre 2 et 6 likes (adapté au nombre d'utilisateurs)
            $availableUsers = $users->where('id', '!=', $article->user_id);
            $maxLikes = min(6, $availableUsers->count());
            $likeCount = rand(2, $maxLikes);
            $likedUsers = $availableUsers->random($likeCount);
            
            foreach ($likedUsers as $user) {
                Like::create([
                    'user_id' => $user->id,
                    'likeable_type' => 'App\\Models\\Article',
                    'likeable_id' => $article->id,
                    'created_at' => now()->subDays(rand(1, 30))->subHours(rand(1, 23)),
                ]);
            }
        }

        // Likes sur les commentaires
        foreach ($comments->take(10) as $comment) {
            // Chaque commentaire reçoit entre 0 et 3 likes (adapté au nombre d'utilisateurs)
            $availableUsers = $users->where('id', '!=', $comment->user_id);
            $maxLikes = min(3, $availableUsers->count());
            $likeCount = rand(0, $maxLikes);
            
            if ($likeCount > 0) {
                $likedUsers = $availableUsers->random($likeCount);
                
                foreach ($likedUsers as $user) {
                    Like::create([
                        'user_id' => $user->id,
                        'likeable_type' => 'App\\Models\\Comment',
                        'likeable_id' => $comment->id,
                        'created_at' => $comment->created_at->addHours(rand(1, 48)),
                    ]);
                }
            }
        }
    }
}
