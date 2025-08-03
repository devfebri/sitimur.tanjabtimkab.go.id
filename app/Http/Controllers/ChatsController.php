<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ChatsController extends Controller
{
    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Restrict access to PPK and Pokja Pemilihan only
        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan'])) {
            abort(403, 'Akses ditolak. Fitur chat hanya tersedia untuk PPK dan Pokja Pemilihan.');
        }

        // Get user info for chat context
        $user = Auth::user();
        $userRole = $user->role;
        $userName = $user->name;
        
        return view('chats', compact('userRole', 'userName'));
    }

    public function getChatUsers(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Restrict access to PPK and Pokja Pemilihan only
        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $currentUser = Auth::user();
        $search = $request->get('search', '');

        // Get users that can chat (PPK and Pokja Pemilihan only)
        // Exclude current user
        $query = User::where('id', '!=', $currentUser->id)
                    ->whereIn('role', ['ppk', 'pokjapemilihan']);

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $users = $query->select('id', 'name', 'username', 'role')
                      ->orderBy('name')
                      ->limit(20)
                      ->get();

        // Format the response
        $formattedUsers = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role === 'ppk' ? 'PPK' : 'Pokja Pemilihan',
                'initials' => strtoupper(substr($user->name, 0, 1))
            ];
        });

        return response()->json([
            'users' => $users
        ]);
    }
    
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $unreadCount = 0;
        
        if (in_array($user->role, ['ppk', 'pokjapemilihan'])) {
            $conversationIds = \App\Models\ChatConversation::whereJsonContains('participants', $user->id)
                ->pluck('id');
            
            $unreadCount = \App\Models\ChatMessage::whereIn('conversation_id', $conversationIds)
                ->where('user_id', '!=', $user->id)
                ->whereNull('read_at')
                ->count();
        }
        
        return response()->json(['count' => $unreadCount]);
    }
}
