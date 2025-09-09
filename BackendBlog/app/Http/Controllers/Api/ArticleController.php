<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Liste des articles publics
     * GET /api/v1/articles
     */
    public function index(Request $request)
    {
        try {
            $query = Article::with(['user', 'tags'])
                           ->where('status', 'published')
                           ->where('visibility', 'public');

            // Filtres optionnels
            if ($request->has('tag')) {
                $query->whereHas('tags', function($q) use ($request) {
                    $q->where('slug', $request->tag);
                });
            }

            if ($request->has('author')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('username', $request->author);
                });
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }

            $articles = $query->orderBy('published_at', 'desc')
                             ->paginate($request->get('limit', 10));

            return response()->json([
                'success' => true,
                'message' => 'Articles récupérés avec succès',
                'data' => $articles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des articles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affichage d'un article
     * GET /api/v1/articles/{id}
     */
    public function show($id)
    {
        try {
            $article = Article::with(['user', 'tags', 'comments.user', 'likes'])
                             ->findOrFail($id);

            // Vérification de la visibilité
            if ($article->status !== 'published' || $article->visibility !== 'public') {
                return response()->json([
                    'success' => false,
                    'message' => 'Article non accessible'
                ], 403);
            }

            // Incrémentation des vues
            $article->increment('views');

            return response()->json([
                'success' => true,
                'message' => 'Article récupéré avec succès',
                'data' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Création d'un article
     * POST /api/v1/articles
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'excerpt' => 'required|string|max:500',
                'content' => 'required|string',
                'status' => 'required|in:draft,published',
                'visibility' => 'required|in:public,private',
                'comments_enabled' => 'boolean',
                'featured' => 'boolean',
                'tags' => 'array',
                'tags.*' => 'string|max:50',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Création de l'article
            $article = Article::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'status' => $request->status,
                'visibility' => $request->visibility,
                'comments_enabled' => $request->get('comments_enabled', true),
                'featured' => $request->get('featured', false),
                'published_at' => $request->published_at ?? ($request->status === 'published' ? now() : null),
            ]);

            // Gestion des tags
            if ($request->has('tags')) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['slug' => Str::slug($tagName), 'color' => '#3B82F6']
                    );
                    $tagIds[] = $tag->id;
                }
                $article->tags()->sync($tagIds);
            }

            $article->load(['user', 'tags']);

            return response()->json([
                'success' => true,
                'message' => 'Article créé avec succès',
                'data' => $article
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour d'un article
     * PUT /api/v1/articles/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Vérification des permissions
            if ($article->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier cet article'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'excerpt' => 'sometimes|required|string|max:500',
                'content' => 'sometimes|required|string',
                'status' => 'sometimes|required|in:draft,published',
                'visibility' => 'sometimes|required|in:public,private',
                'comments_enabled' => 'boolean',
                'featured' => 'boolean',
                'tags' => 'array',
                'tags.*' => 'string|max:50',
                'published_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mise à jour de l'article
            $updateData = $request->only([
                'title', 'excerpt', 'content', 'status', 'visibility', 
                'comments_enabled', 'featured', 'published_at'
            ]);

            if ($request->has('title')) {
                $updateData['slug'] = Str::slug($request->title);
            }

            if ($request->has('status') && $request->status === 'published' && !$article->published_at) {
                $updateData['published_at'] = now();
            }

            $article->update($updateData);

            // Gestion des tags
            if ($request->has('tags')) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['slug' => Str::slug($tagName), 'color' => '#3B82F6']
                    );
                    $tagIds[] = $tag->id;
                }
                $article->tags()->sync($tagIds);
            }

            $article->load(['user', 'tags']);

            return response()->json([
                'success' => true,
                'message' => 'Article mis à jour avec succès',
                'data' => $article
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'un article
     * DELETE /api/v1/articles/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Vérification des permissions
            if ($article->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer cet article'
                ], 403);
            }

            $article->delete();

            return response()->json([
                'success' => true,
                'message' => 'Article supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'article',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like d'un article
     * POST /api/v1/articles/{id}/like
     */
    public function like(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Vérification si déjà liké
            $existingLike = $user->likes()->where('article_id', $id)->first();
            
            if ($existingLike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article déjà liké'
                ], 400);
            }

            // Création du like
            $user->likes()->create([
                'article_id' => $id
            ]);

            // Mise à jour du compteur
            $article->increment('likes_count');

            return response()->json([
                'success' => true,
                'message' => 'Article liké avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlike d'un article
     * POST /api/v1/articles/{id}/unlike
     */
    public function unlike(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Suppression du like
            $like = $user->likes()->where('article_id', $id)->first();
            
            if (!$like) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article non liké'
                ], 400);
            }

            $like->delete();

            // Mise à jour du compteur
            $article->decrement('likes_count');

            return response()->json([
                'success' => true,
                'message' => 'Like supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du like',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Signet d'un article
     * POST /api/v1/articles/{id}/bookmark
     */
    public function bookmark(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Vérification si déjà en signet
            $existingBookmark = $user->bookmarks()->where('article_id', $id)->first();
            
            if ($existingBookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article déjà en signet'
                ], 400);
            }

            // Création du signet
            $user->bookmarks()->create([
                'article_id' => $id
            ]);

            // Mise à jour du compteur
            $article->increment('bookmarks_count');

            return response()->json([
                'success' => true,
                'message' => 'Article ajouté aux signets avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout aux signets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'un signet
     * POST /api/v1/articles/{id}/unbookmark
     */
    public function unbookmark(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($id);

            // Suppression du signet
            $bookmark = $user->bookmarks()->where('article_id', $id)->first();
            
            if (!$bookmark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Article non en signet'
                ], 400);
            }

            $bookmark->delete();

            // Mise à jour du compteur
            $article->decrement('bookmarks_count');

            return response()->json([
                'success' => true,
                'message' => 'Signet supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du signet',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}