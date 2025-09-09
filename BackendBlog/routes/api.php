<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NotificationController;

/*
|--------------------------------------------------------------------------
| API Routes - Blog Personnel
|--------------------------------------------------------------------------
|
| Routes API simples et propres pour le blog personnel
| Structure: /api/v1/...
|
*/

// Versioning de l'API
Route::prefix('v1')->group(function () {
    
    // ========================================
    // ROUTES PUBLIQUES (sans authentification)
    // ========================================
    
    // Authentification publique
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });
    
    // Articles publics
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('articles/{id}', [ArticleController::class, 'show']);
    
    // Utilisateurs publics
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::get('users/{id}/articles', [UserController::class, 'publicArticles']);
    
    // ========================================
    // ROUTES PROTÉGÉES (avec authentification)
    // ========================================
    
    Route::middleware('auth:sanctum')->group(function () {
        
        // Authentification protégée
        Route::prefix('auth')->group(function () {
            Route::get('user', [AuthController::class, 'user']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        });
        
        // Gestion du profil utilisateur
        Route::prefix('user')->group(function () {
            Route::get('profile', [UserController::class, 'profile']);
            Route::put('profile', [UserController::class, 'updateProfile']);
            Route::put('password', [UserController::class, 'updatePassword']);
            Route::get('articles', [UserController::class, 'myArticles']);
            Route::get('bookmarks', [UserController::class, 'bookmarks']);
        });
        
        // Gestion des articles (CRUD complet)
        Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
        Route::post('articles/{id}/like', [ArticleController::class, 'like']);
        Route::post('articles/{id}/bookmark', [ArticleController::class, 'bookmark']);
        Route::post('articles/{id}/unlike', [ArticleController::class, 'unlike']);
        Route::post('articles/{id}/unbookmark', [ArticleController::class, 'unbookmark']);
        
        // Gestion des commentaires
        Route::prefix('articles/{articleId}')->group(function () {
            Route::get('comments', [CommentController::class, 'index']);
            Route::post('comments', [CommentController::class, 'store']);
        });
        Route::apiResource('comments', CommentController::class)->except(['index', 'store']);
        Route::post('comments/{id}/like', [CommentController::class, 'like']);
        Route::post('comments/{id}/unlike', [CommentController::class, 'unlike']);
        
        // Gestion des amis
        Route::prefix('friends')->group(function () {
            Route::get('/', [FriendController::class, 'index']);
            Route::post('/', [FriendController::class, 'store']);
            Route::get('search', [FriendController::class, 'search']);
            Route::get('requests', [FriendController::class, 'requests']);
        });
        Route::apiResource('friends', FriendController::class)->except(['index', 'store']);
        Route::post('friends/{id}/accept', [FriendController::class, 'accept']);
        Route::post('friends/{id}/reject', [FriendController::class, 'reject']);
        
        // Gestion des notifications
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
            Route::post('{id}/mark-read', [NotificationController::class, 'markAsRead']);
        });
        Route::apiResource('notifications', NotificationController::class)->except(['index']);
        
        // Routes d'administration (optionnel)
        Route::prefix('admin')->middleware('admin')->group(function () {
            Route::get('users', [UserController::class, 'adminIndex']);
            Route::post('users', [UserController::class, 'adminStore']);
            Route::delete('users/{id}', [UserController::class, 'adminDestroy']);
        });
    });
});

// ========================================
// ROUTES DE TEST ET DEBUG
// ========================================

Route::prefix('test')->group(function () {
    Route::get('public', function () {
        return response()->json([
            'success' => true,
            'message' => 'API publique accessible',
            'data' => [
                'timestamp' => now(),
                'version' => '1.0.0'
            ]
        ]);
    });
    
    Route::get('debug', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Debug info',
            'data' => [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'timestamp' => now()
            ]
        ]);
    });
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('protected', function (Request $request) {
            return response()->json([
                'success' => true,
                'message' => 'API protégée accessible',
                'data' => [
                    'user' => $request->user(),
                    'timestamp' => now()
                ]
            ]);
        });
    });
});