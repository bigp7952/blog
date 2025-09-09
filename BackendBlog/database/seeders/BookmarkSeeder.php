<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bookmark;
use App\Models\Article;
use App\Models\User;

class BookmarkSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $articles = Article::all();

        if ($users->isEmpty() || $articles->isEmpty()) {
            return;
        }

        // Chaque utilisateur sauvegarde entre 2 et 8 articles
        foreach ($users as $user) {
            $bookmarkCount = rand(2, 8);
            $bookmarkedArticles = $articles->where('user_id', '!=', $user->id)->random($bookmarkCount);
            
            foreach ($bookmarkedArticles as $article) {
                Bookmark::create([
                    'user_id' => $user->id,
                    'article_id' => $article->id,
                    'created_at' => now()->subDays(rand(1, 20))->subHours(rand(1, 23)),
                ]);
            }
        }

        // Certains articles sont plus populaires (plus de bookmarks)
        $popularArticles = $articles->take(3);
        foreach ($popularArticles as $article) {
            $additionalBookmarks = rand(3, 6);
            $additionalUsers = $users->where('id', '!=', $article->user_id)->random($additionalBookmarks);
            
            foreach ($additionalUsers as $user) {
                // Vérifier si l'utilisateur n'a pas déjà bookmarké cet article
                $existingBookmark = Bookmark::where('user_id', $user->id)
                    ->where('article_id', $article->id)
                    ->first();
                
                if (!$existingBookmark) {
                    Bookmark::create([
                        'user_id' => $user->id,
                        'article_id' => $article->id,
                        'created_at' => now()->subDays(rand(1, 15))->subHours(rand(1, 23)),
                    ]);
                }
            }
        }
    }
}

