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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::with(['user', 'tags'])
                       ->published()
                       ->public();

        // Filter by status for authenticated users
        if ($request->user()) {
            $query = Article::with(['user', 'tags']);
            
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('visibility')) {
                $query->where('visibility', $request->visibility);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'published_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $articles = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $articles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
            'visibility' => 'required|in:public,private',
            'comments_enabled' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $article = Article::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'status' => $request->status,
            'visibility' => $request->visibility,
            'comments_enabled' => $request->get('comments_enabled', true),
            'published_at' => $request->status === 'published' ? now() : null,
            'scheduled_at' => $request->scheduled_at,
        ]);

        // Handle tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $article->tags()->sync($tagIds);
        }

        $article->load(['user', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Article created successfully',
            'data' => $article
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $query = Article::with(['user', 'tags', 'comments.user']);
        
        // For authenticated users, show all articles
        if ($request->user()) {
            $article = $query->findOrFail($id);
        } else {
            // For public access, only show published and public articles
            $article = $query->published()->public()->findOrFail($id);
        }

        // Increment views
        $article->increment('views');

        return response()->json([
            'success' => true,
            'data' => $article
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        
        // Check if user owns the article
        if ($request->user()->id !== $article->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string|max:500',
            'content' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:draft,published,scheduled',
            'visibility' => 'sometimes|required|in:public,private',
            'comments_enabled' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'title', 'excerpt', 'content', 'status', 'visibility', 'comments_enabled', 'scheduled_at'
        ]);

        // Handle slug update if title changed
        if ($request->has('title')) {
            $updateData['slug'] = Str::slug($request->title);
        }

        // Handle published_at
        if ($request->has('status') && $request->status === 'published' && !$article->published_at) {
            $updateData['published_at'] = now();
        }

        $article->update($updateData);

        // Handle tags
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->tags as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['slug' => Str::slug($tagName)]
                );
                $tagIds[] = $tag->id;
            }
            $article->tags()->sync($tagIds);
        }

        $article->load(['user', 'tags']);

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully',
            'data' => $article
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        
        // Check if user owns the article
        if ($request->user()->id !== $article->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $article->delete();

        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully'
        ]);
    }

    /**
     * Like an article.
     */
    public function like(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $user = $request->user();

        $like = $article->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $article->decrement('likes_count');
            $message = 'Article unliked';
        } else {
            $article->likes()->create(['user_id' => $user->id]);
            $article->increment('likes_count');
            $message = 'Article liked';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'likes_count' => $article->fresh()->likes_count
            ]
        ]);
    }

    /**
     * Bookmark an article.
     */
    public function bookmark(Request $request, string $id)
    {
        $article = Article::findOrFail($id);
        $user = $request->user();

        $bookmark = $user->bookmarks()->where('article_id', $article->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $article->decrement('bookmarks_count');
            $message = 'Article unbookmarked';
        } else {
            $user->bookmarks()->create(['article_id' => $article->id]);
            $article->increment('bookmarks_count');
            $message = 'Article bookmarked';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'bookmarks_count' => $article->fresh()->bookmarks_count
            ]
        ]);
    }
}
