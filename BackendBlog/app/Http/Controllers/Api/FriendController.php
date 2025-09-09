<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    /**
     * Liste des amis de l'utilisateur connecté
     * GET /api/v1/friends
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            // Amis acceptés
            $friends = $user->friends()
                           ->wherePivot('status', 'accepted')
                           ->with(['avatar', 'is_online', 'last_seen'])
                           ->get();

            return response()->json([
                'success' => true,
                'message' => 'Amis récupérés avec succès',
                'data' => $friends
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des amis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoi d'une demande d'ami
     * POST /api/v1/friends
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
                'friend_id' => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $friendId = $request->friend_id;

            // Vérifications
            if ($user->id === $friendId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de s\'ajouter soi-même comme ami'
                ], 400);
            }

            // Vérifier si une demande existe déjà
            $existingRequest = Friend::where(function($query) use ($user, $friendId) {
                $query->where('user_id', $user->id)->where('friend_id', $friendId);
            })->orWhere(function($query) use ($user, $friendId) {
                $query->where('user_id', $friendId)->where('friend_id', $user->id);
            })->first();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande d\'ami déjà existante'
                ], 400);
            }

            // Création de la demande d'ami
            $friendRequest = Friend::create([
                'user_id' => $user->id,
                'friend_id' => $friendId,
                'status' => 'pending'
            ]);

            // Création de la notification
            Notification::create([
                'user_id' => $friendId,
                'type' => 'friend_request',
                'title' => 'Nouvelle demande d\'ami',
                'message' => $user->name . ' souhaite vous ajouter comme ami',
                'metadata' => [
                    'user_id' => $user->id,
                    'friend_request_id' => $friendRequest->id
                ]
            ]);

            $friendRequest->load('friend');

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami envoyée avec succès',
                'data' => $friendRequest
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la demande d\'ami',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche d'utilisateurs pour ajouter comme amis
     * GET /api/v1/friends/search
     */
    public function search(Request $request)
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
                'query' => 'required|string|min:2',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = $request->query;

            $users = User::where('id', '!=', $user->id)
                        ->where(function($q) use ($query) {
                            $q->where('name', 'like', "%{$query}%")
                              ->orWhere('username', 'like', "%{$query}%")
                              ->orWhere('email', 'like', "%{$query}%");
                        })
                        ->with(['avatar', 'is_online', 'last_seen'])
                        ->limit(10)
                        ->get();

            // Ajouter le statut d'amitié
            $users->each(function($searchedUser) use ($user) {
                $friendship = Friend::where(function($query) use ($user, $searchedUser) {
                    $query->where('user_id', $user->id)->where('friend_id', $searchedUser->id);
                })->orWhere(function($query) use ($user, $searchedUser) {
                    $query->where('user_id', $searchedUser->id)->where('friend_id', $user->id);
                })->first();

                $searchedUser->friendship_status = $friendship ? $friendship->status : 'none';
            });

            return response()->json([
                'success' => true,
                'message' => 'Recherche effectuée avec succès',
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Demandes d'ami reçues
     * GET /api/v1/friends/requests
     */
    public function requests(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $requests = $user->receivedFriendRequests()
                           ->with('user')
                           ->where('status', 'pending')
                           ->get();

            return response()->json([
                'success' => true,
                'message' => 'Demandes d\'ami récupérées avec succès',
                'data' => $requests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accepter une demande d'ami
     * POST /api/v1/friends/{id}/accept
     */
    public function accept(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $friendRequest = Friend::findOrFail($id);

            // Vérification des permissions
            if ($friendRequest->friend_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            // Mise à jour du statut
            $friendRequest->update([
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            // Création de l'amitié mutuelle
            Friend::create([
                'user_id' => $user->id,
                'friend_id' => $friendRequest->user_id,
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            // Notification pour l'expéditeur
            Notification::create([
                'user_id' => $friendRequest->user_id,
                'type' => 'friend_accepted',
                'title' => 'Demande d\'ami acceptée',
                'message' => $user->name . ' a accepté votre demande d\'ami',
                'metadata' => [
                    'user_id' => $user->id
                ]
            ]);

            $friendRequest->load(['user', 'friend']);

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami acceptée avec succès',
                'data' => $friendRequest
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'acceptation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeter une demande d'ami
     * POST /api/v1/friends/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $friendRequest = Friend::findOrFail($id);

            // Vérification des permissions
            if ($friendRequest->friend_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $friendRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande d\'ami rejetée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du rejet',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Suppression d'un ami
     * DELETE /api/v1/friends/{id}
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

            $friendRequest = Friend::findOrFail($id);

            // Vérification des permissions
            if ($friendRequest->user_id !== $user->id && $friendRequest->friend_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            // Si c'est une amitié acceptée, supprimer les deux côtés
            if ($friendRequest->status === 'accepted') {
                Friend::where('user_id', $friendRequest->friend_id)
                      ->where('friend_id', $friendRequest->user_id)
                      ->delete();
            }

            $friendRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Amitié supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}