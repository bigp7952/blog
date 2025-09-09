<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Liste des notifications de l'utilisateur connecté
     * GET /api/v1/notifications
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
            
            $notifications = $user->notifications()
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);

            return response()->json([
                'success' => true,
                'message' => 'Notifications récupérées avec succès',
                'data' => $notifications
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nombre de notifications non lues
     * GET /api/v1/notifications/unread-count
     */
    public function unreadCount(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $count = $user->notifications()->where('read_at', null)->count();

            return response()->json([
                'success' => true,
                'message' => 'Nombre de notifications non lues récupéré',
                'data' => [
                    'unread_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du nombre de notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer toutes les notifications comme lues
     * POST /api/v1/notifications/mark-all-read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $user->notifications()
                ->where('read_at', null)
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Toutes les notifications ont été marquées comme lues'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer une notification comme lue
     * POST /api/v1/notifications/{id}/mark-read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $notification = $user->notifications()->findOrFail($id);
            
            if (!$notification->read_at) {
                $notification->update(['read_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affichage d'une notification
     * GET /api/v1/notifications/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            
            $notification = $user->notifications()->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Notification récupérée avec succès',
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notification non trouvée',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Suppression d'une notification
     * DELETE /api/v1/notifications/{id}
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
            
            $notification = $user->notifications()->findOrFail($id);
            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}