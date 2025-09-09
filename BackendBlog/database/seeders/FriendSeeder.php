<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Friend;
use App\Models\User;

class FriendSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            return;
        }

        // Créer des amitiés acceptées
        $friendships = [
            [1, 2], // Aminata et Moussa
            [1, 3], // Aminata et Fatou
            [1, 4], // Aminata et Ibrahima
            [2, 3], // Moussa et Fatou
            [2, 5], // Moussa et Aïcha
            [3, 4], // Fatou et Ibrahima
            [3, 6], // Fatou et Cheikh
            [4, 5], // Ibrahima et Aïcha
            [4, 7], // Ibrahima et Mariama
            [5, 6], // Aïcha et Cheikh
            [5, 8], // Aïcha et Ousmane
            [6, 7], // Cheikh et Mariama
            [6, 8], // Cheikh et Ousmane
            [7, 8], // Mariama et Ousmane
        ];

        foreach ($friendships as [$userId, $friendId]) {
            if ($users->find($userId) && $users->find($friendId)) {
                // Créer l'amitié dans les deux sens
                Friend::create([
                    'user_id' => $userId,
                    'friend_id' => $friendId,
                    'status' => 'accepted',
                    'accepted_at' => now()->subDays(rand(1, 30)),
                ]);

                Friend::create([
                    'user_id' => $friendId,
                    'friend_id' => $userId,
                    'status' => 'accepted',
                    'accepted_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Créer quelques demandes d'amitié en attente
        $pendingRequests = [
            [1, 6], // Aminata demande à Cheikh
            [2, 7], // Moussa demande à Mariama
            [3, 8], // Fatou demande à Ousmane
            [4, 6], // Ibrahima demande à Cheikh
            [5, 7], // Aïcha demande à Mariama
        ];

        foreach ($pendingRequests as [$userId, $friendId]) {
            if ($users->find($userId) && $users->find($friendId)) {
                Friend::create([
                    'user_id' => $userId,
                    'friend_id' => $friendId,
                    'status' => 'pending',
                ]);
            }
        }

        // Créer quelques utilisateurs bloqués (rare)
        $blockedUsers = [
            [1, 8], // Aminata bloque Ousmane (exemple)
        ];

        foreach ($blockedUsers as [$userId, $friendId]) {
            if ($users->find($userId) && $users->find($friendId)) {
                Friend::create([
                    'user_id' => $userId,
                    'friend_id' => $friendId,
                    'status' => 'blocked',
                ]);
            }
        }
    }
}

