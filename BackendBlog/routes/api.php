<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public article routes
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);

// Public user routes
Route::get('users/{id}', [UserController::class, 'show']);
Route::get('users/{id}/articles', [UserController::class, 'articles']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('user', [AuthController::class, 'user']);
    });

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::put('password', [UserController::class, 'updatePassword']);
        Route::get('bookmarks', [UserController::class, 'bookmarks']);
    });

    // Article routes
    Route::apiResource('articles', ArticleController::class)->except(['index', 'show']);
    Route::post('articles/{id}/like', [ArticleController::class, 'like']);
    Route::post('articles/{id}/bookmark', [ArticleController::class, 'bookmark']);

    // Comment routes
    Route::prefix('articles/{articleId}')->group(function () {
        Route::get('comments', [CommentController::class, 'index']);
        Route::post('comments', [CommentController::class, 'store']);
    });
    Route::apiResource('comments', CommentController::class)->except(['index', 'store']);
    Route::post('comments/{id}/like', [CommentController::class, 'like']);

    // Friend routes
    Route::prefix('friends')->group(function () {
        Route::get('/', [FriendController::class, 'index']);
        Route::post('/', [FriendController::class, 'store']);
        Route::get('search', [FriendController::class, 'search']);
    });
    Route::apiResource('friends', FriendController::class)->except(['index', 'store']);

    // Notification routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::post('{id}/mark-read', [NotificationController::class, 'markAsRead']);
    });
    Route::apiResource('notifications', NotificationController::class)->except(['index']);

    // Admin routes (if needed)
    Route::prefix('admin')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
    });
});
