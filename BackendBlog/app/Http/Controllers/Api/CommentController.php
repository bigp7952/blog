<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Liste des commentaires d'un article
     * GET /api/v1/articles/{articleId}/comments
     */
    public function index(Request $request, $articleId)
    {
        try {
            $article = Article::findOrFail($articleId);

            // Vérification de la visibilité
            if ($article->status !== 'published' || $article->visibility !== 'public') {
                return response()->json([
                    'success' => false,
                    'message' => 'Article non accessible'
                ], 403);
            }

            $comments = Comment::with(['user', 'replies.user'])
                             ->where('article_id', $articleId)
                             ->whereNull('parent_id')
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Commentaires récupérés avec succès',
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des commentaires',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Création d'un commentaire
     * POST /api/v1/articles/{articleId}/comments
     */
    public function store(Request $request, $articleId)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $article = Article::findOrFail($articleId);

            // Vérification si les commentaires sont autorisés
            if (!$article->comments_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Les commentaires sont désactivés pour cet article'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:1000',
                'parent_id' => 'nullable|exists:comments,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Création du commentaire
            $comment = Comment::create([
                'user_id' => $user->id,
                'article_id' => $articleId,
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            // Mise à jour du compteur
            $article->increment('comments_count');

            $comment->load(['user', 'replies.user']);

            return response()->json([
                'success' => true,
                'message' => 'Commentaire créé avec succès',
                'data' => $comment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du commentaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affichage d'un commentaire
     * GET /api/v1/comments/{id}
     */
    public function show($id)
    {
        try {
            $comment = Comment::with(['user', 'article', 'replies.user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Commentaire récupéré avec succès',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Commentaire non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mise à jour d'un commentaire
     * PUT /api/v1/comments/{id}
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

            $comment = Comment::findOrFail($id);

            // Vérification des permissions
            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à modifier ce commentaire'
                ], 403);
            }

            // Validation des données
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mise à jour du commentaire
            $comment->update([
                'content' => $request->content,
            ]);

            $comment->load(['user', 'replies.user']);

            return response()->json([
                'success' => true,
                'message' => 'Commentaire mis à jour avec succès',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du commentaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'un commentaire
     * DELETE /api/v1/comments/{id}
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

            $comment = Comment::findOrFail($id);

            // Vérification des permissions
            if ($comment->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé à supprimer ce commentaire'
                ], 403);
            }

            // Suppression des réponses si c'est un commentaire parent
            if ($comment->parent_id === null) {
                Comment::where('parent_id', $comment->id)->delete();
            }

            // Mise à jour du compteur de l'article
            $comment->article->decrement('comments_count');

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Commentaire supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du commentaire',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Like d'un commentaire
     * POST /api/v1/comments/{id}/like
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

            $comment = Comment::findOrFail($id);

            // Vérification si déjà liké
            $existingLike = $user->likes()->where('comment_id', $id)->first();
            
            if ($existingLike) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commentaire déjà liké'
                ], 400);
            }

            // Création du like
            $user->likes()->create([
                'comment_id' => $id
            ]);

            // Mise à jour du compteur
            $comment->increment('likes_count');

            return response()->json([
                'success' => true,
                'message' => 'Commentaire liké avec succès'
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
     * Unlike d'un commentaire
     * POST /api/v1/comments/{id}/unlike
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

            $comment = Comment::findOrFail($id);

            // Suppression du like
            $like = $user->likes()->where('comment_id', $id)->first();
            
            if (!$like) {
                return response()->json([
                    'success' => false,
                    'message' => 'Commentaire non liké'
                ], 400);
            }

            $like->delete();

            // Mise à jour du compteur
            $comment->decrement('likes_count');

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
}