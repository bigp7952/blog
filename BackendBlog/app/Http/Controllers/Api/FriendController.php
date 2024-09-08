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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $friends = $user->friends()
                       ->wherePivot('status', 'accepted')
                       ->with(['avatar', 'is_online', 'last_seen'])
                       ->get();

        $pendingReceived = $user->receivedFriendRequests()
                               ->with('user')
                               ->where('status', 'pending')
                               ->get();

        $pendingSent = $user->sentFriendRequests()
                           ->with('friend')
                           ->where('status', 'pending')
                           ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'friends' => $friends,
                'pending_received' => $pendingReceived,
                'pending_sent' => $pendingSent
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $friendId = $request->friend_id;

        // Check if trying to add self
        if ($user->id === $friendId) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add yourself as a friend'
            ], 400);
        }

        // Check if friendship already exists
        $existingFriendship = Friend::where(function($query) use ($user, $friendId) {
            $query->where('user_id', $user->id)->where('friend_id', $friendId);
        })->orWhere(function($query) use ($user, $friendId) {
            $query->where('user_id', $friendId)->where('friend_id', $user->id);
        })->first();

        if ($existingFriendship) {
            return response()->json([
                'success' => false,
                'message' => 'Friendship already exists'
            ], 400);
        }

        // Create friend request
        $friendRequest = Friend::create([
            'user_id' => $user->id,
            'friend_id' => $friendId,
            'status' => 'pending'
        ]);

        // Create notification for the friend
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
            'message' => 'Friend request sent successfully',
            'data' => $friendRequest
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $friend = Friend::with(['user', 'friend'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $friend
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $friendRequest = Friend::findOrFail($id);
        $user = $request->user();

        // Check if user is the recipient of the friend request
        if ($friendRequest->friend_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:accepted,blocked',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $friendRequest->update([
            'status' => $request->status,
            'accepted_at' => $request->status === 'accepted' ? now() : null
        ]);

        // If accepted, create mutual friendship
        if ($request->status === 'accepted') {
            Friend::create([
                'user_id' => $user->id,
                'friend_id' => $friendRequest->user_id,
                'status' => 'accepted',
                'accepted_at' => now()
            ]);

            // Create notification for the sender
            Notification::create([
                'user_id' => $friendRequest->user_id,
                'type' => 'friend_accepted',
                'title' => 'Demande d\'ami acceptée',
                'message' => $user->name . ' a accepté votre demande d\'ami',
                'metadata' => [
                    'user_id' => $user->id
                ]
            ]);
        }

        $friendRequest->load(['user', 'friend']);

        return response()->json([
            'success' => true,
            'message' => 'Friend request updated successfully',
            'data' => $friendRequest
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $friendRequest = Friend::findOrFail($id);
        $user = $request->user();

        // Check if user is involved in the friendship
        if ($friendRequest->user_id !== $user->id && $friendRequest->friend_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // If it's an accepted friendship, remove both sides
        if ($friendRequest->status === 'accepted') {
            Friend::where('user_id', $friendRequest->friend_id)
                  ->where('friend_id', $friendRequest->user_id)
                  ->delete();
        }

        $friendRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Friendship removed successfully'
        ]);
    }

    /**
     * Search for users to add as friends.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
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

        // Add friendship status to each user
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
            'data' => $users
        ]);
    }
}