<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $articleId)
    {
        $article = Article::findOrFail($articleId);
        
        $comments = $article->comments()
                           ->with(['user', 'replies.user'])
                           ->topLevel()
                           ->approved()
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => $comments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $articleId)
    {
        $article = Article::findOrFail($articleId);
        
        // Check if comments are enabled
        if (!$article->comments_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Comments are disabled for this article'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Increment comments count
        $article->increment('comments_count');

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'data' => $comment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $comment = Comment::with(['user', 'replies.user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Check if user owns the comment
        if ($request->user()->id !== $comment->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $comment->update([
            'content' => $request->content,
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully',
            'data' => $comment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);
        
        // Check if user owns the comment or is admin
        if ($request->user()->id !== $comment->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Decrement comments count
        $comment->article->decrement('comments_count');

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Like a comment.
     */
    public function like(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);
        $user = $request->user();

        $like = $comment->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            $message = 'Comment unliked';
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $message = 'Comment liked';
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
