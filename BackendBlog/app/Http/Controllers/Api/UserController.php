<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Affichage d'un utilisateur public
     * GET /api/v1/users/{id}
     */
    public function show($id)
    {
        try {
            $user = User::with(['articles' => function($query) {
                $query->where('status', 'published')
                      ->where('visibility', 'public')
                      ->latest();
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur récupéré avec succès',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Articles publics d'un utilisateur
     * GET /api/v1/users/{id}/articles
     */
    public function publicArticles($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $articles = $user->articles()
                           ->with(['tags'])
                           ->where('status', 'published')
                           ->where('visibility', 'public')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Articles publics récupérés avec succès',
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
     * Profil de l'utilisateur connecté
     * GET /api/v1/user/profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $user->load(['articles' => function($query) {
                $query->latest();
            }]);

            return response()->json([
                'success' => true,
                'message' => 'Profil récupéré avec succès',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour du profil
     * PUT /api/v1/user/profile
     */
    public function updateProfile(Request $request)
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
                'name' => 'sometimes|required|string|max:255',
                'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
                'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'bio' => 'nullable|string|max:500',
                'avatar' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mise à jour du profil
            $user->update($request->only([
                'name', 'username', 'email', 'phone', 'bio', 'avatar'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour du mot de passe
     * PUT /api/v1/user/password
     */
    public function updatePassword(Request $request)
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
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérification du mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe actuel incorrect'
                ], 400);
            }

            // Mise à jour du mot de passe
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du mot de passe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Articles de l'utilisateur connecté
     * GET /api/v1/user/articles
     */
    public function myArticles(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $query = $user->articles()->with(['tags', 'likes', 'comments', 'bookmarks']);
            
            // Filtres optionnels
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }
            
            if ($request->has('visibility') && $request->visibility !== 'all') {
                $query->where('visibility', $request->visibility);
            }
            
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            }
            
            $articles = $query->orderBy('created_at', 'desc')
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
     * Signets de l'utilisateur connecté
     * GET /api/v1/user/bookmarks
     */
    public function bookmarks(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $bookmarks = $user->bookmarks()
                             ->with(['article.user', 'article.tags'])
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Signets récupérés avec succès',
                'data' => $bookmarks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des signets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des utilisateurs (admin)
     * GET /api/v1/admin/users
     */
    public function adminIndex(Request $request)
    {
        try {
            $users = User::with(['articles'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateurs récupérés avec succès',
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Création d'un utilisateur (admin)
     * POST /api/v1/admin/users
     */
    public function adminStore(Request $request)
    {
        try {
            // Validation des données
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'phone' => 'nullable|string|max:20',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'un utilisateur (admin)
     * DELETE /api/v1/admin/users/{id}
     */
    public function adminDestroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}