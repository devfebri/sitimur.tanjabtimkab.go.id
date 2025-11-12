<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ChatsController extends Controller
{
    public function index($pengajuanId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Restrict access to PPK, Verifikator, and Pokja Pemilihan only
        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan', 'verifikator'])) {
            abort(403, 'Akses ditolak. Fitur chat hanya tersedia untuk PPK, Verifikator, dan Pokja Pemilihan.');
        }

        // Get user info for chat context
        $user = Auth::user();
        $userRole = $user->role;
        $userName = $user->name;

        // Get pengajuan data
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        // Tentukan chat_type berdasarkan status pengajuan dan role user
        $chatType = 'verifikator';
        if ($userRole === 'pokjapemilihan') {
            $chatType = 'pokja';
        } elseif ($userRole === 'ppk') {
            $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
        }

        $chatMessages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', $chatType)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Determine return route based on user role
        $returnRoute = $userRole === 'ppk'
            ? route('ppk_pengajuanopen', ['id' => $pengajuanId])
            : route('pokjapemilihan_pengajuanopen', ['id' => $pengajuanId]);

        return view('chatsnew', compact('userRole', 'userName', 'pengajuan', 'returnRoute', 'chatMessages', 'chatType'));
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



    public function sendMessage(Request $request, $pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan', 'verifikator'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $validatedData = $request->validate([
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png'
        ]);

        // Ensure pengajuan exists and user has access
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        $user = Auth::user();
        $filePath = null;
        $fileName = null;

        // Tentukan chat_type berdasarkan status pengajuan
        // Status < 20: Masih di verifikator (verifikator + PPK)
        // Status >= 20: Sudah di pokja (pokja1/2/3 + PPK)
        $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';

        // Handle file upload if present
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();

                // Create unique filename
                $uniqueName = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Build folder path
                $dateFolder = $pengajuan->created_at->format('Y/m/d');
                $userFolder = $pengajuan->user->username;
                $pengajuanFolder = $pengajuan->id;
                $folderPath = "pengajuan/{$dateFolder}/{$userFolder}/{$pengajuanFolder}/chat_uploads";

                // Store file using Storage facade (stores in storage/app/public)
                $path = $file->storeAs($folderPath, $uniqueName, 'public');

                // Set file path relative to storage folder
                $filePath = $path;

            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 400);
            }
        }

        // Create chat message dengan chat_type
        $message = new \App\Models\ChatMessage([
            'pengajuan_id' => $pengajuanId,
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'file_path' => $filePath,
            'chat_type' => $chatType,
        ]);
        $message->save();

        // Reload message with user relationship
        $message->load('user');

        // Format response
        return response()->json([
            'comment' => [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user' => $message->user,
                'sender_name' => $message->user->name,
                'sender_initial' => strtoupper(substr($message->user->name, 0, 1)),
                'message' => $message->message,
                'created_at' => $message->created_at->format('M j, Y g:i A'),
                'file_path' => $filePath,
                'file_name' => $fileName,
                'chat_type' => $chatType,
            ]
        ]);
    }

    public function getMessages($pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan', 'verifikator'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Ensure pengajuan exists and user has access
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        $user = Auth::user();

        // Tentukan chat_type yang bisa diakses
        if ($user->role === 'verifikator') {
            // Verifikator hanya bisa lihat chat verifikator
            $chatType = 'verifikator';
        } elseif ($user->role === 'pokjapemilihan') {
            // Pokja hanya bisa lihat chat pokja
            $chatType = 'pokja';
        } else { // ppk
            // PPK bisa lihat semua chat sesuai status pengajuan
            $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
        }

        // Get all messages for this pengajuan dengan filter chat_type
        $messages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', $chatType)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedMessages = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'sender_name' => $message->user->name,
                'sender_initial' => strtoupper(substr($message->user->name, 0, 1)),
                'message' => $message->message,
                'created_at' => $message->created_at->format('M j, Y g:i A'),
                'file_path' => $message->file_path,
                'file_name' => $message->file_path ? basename($message->file_path) : null,
                'chat_type' => $message->chat_type,
            ];
        });

        return response()->json(['messages' => $formattedMessages]);
    }

    public function getNewMessages($pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan', 'verifikator'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Get lastId from request
        $lastId = request('last_id', 0);

        // Ensure pengajuan exists and user has access
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        $user = Auth::user();

        // Tentukan chat_type yang bisa diakses
        if ($user->role === 'verifikator') {
            $chatType = 'verifikator';
        } elseif ($user->role === 'pokjapemilihan') {
            $chatType = 'pokja';
        } else { // ppk
            $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
        }

        // Get only new messages dengan filter chat_type
        $messages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', $chatType)
            ->where('id', '>', $lastId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedMessages = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'sender_name' => $message->user->name,
                'sender_initial' => strtoupper(substr($message->user->name, 0, 1)),
                'message' => $message->message,
                'created_at' => $message->created_at->format('M j, Y g:i A'),
                'file_path' => $message->file_path,
                'file_name' => $message->file_path ? basename($message->file_path) : null,
                'chat_type' => $message->chat_type,
            ];
        });

        return response()->json(['messages' => $formattedMessages]);
    }

    /**
     * Get all chat history for verifikator
     * Menampilkan riwayat semua percakapan dengan verifikator untuk setiap pengajuan
     */
    public function chatHistory()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Only verifikator can access this
        if (Auth::user()->role !== 'verifikator') {
            abort(403, 'Akses ditolak. Hanya verifikator yang dapat mengakses chat history.');
        }

        $verifikator = Auth::user();

        // Get all pengajuan where current user is verifikator
        $pengajuans = \App\Models\Pengajuan::whereIn('verifikator_id', [$verifikator->id])
            ->with(['user', 'metode_pengadaan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get chat statistics for each pengajuan
        $chatStats = [];
        foreach ($pengajuans as $pengajuan) {
            $totalMessages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuan->id)
                ->where('chat_type', 'verifikator')
                ->count();

            $unreadMessages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuan->id)
                ->where('chat_type', 'verifikator')
                ->whereNull('read_at')
                ->where('user_id', '!=', $verifikator->id)
                ->count();

            $lastMessage = \App\Models\ChatMessage::where('pengajuan_id', $pengajuan->id)
                ->where('chat_type', 'verifikator')
                ->latest('created_at')
                ->first();

            $chatStats[$pengajuan->id] = [
                'total_messages' => $totalMessages,
                'unread_messages' => $unreadMessages,
                'last_message' => $lastMessage,
                'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
            ];
        }

        return view('chat-history', compact('pengajuans', 'chatStats'));
    }

    /**
     * Get chat messages for specific pengajuan (for chat history view)
     */
    public function chatHistoryMessages($pengajuanId)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Only verifikator can access this
        if (Auth::user()->role !== 'verifikator') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $verifikator = Auth::user();
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        // Check if verifikator is assigned to this pengajuan
        if ($pengajuan->verifikator_id !== $verifikator->id) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $messages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', 'verifikator')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Format messages
        $formattedMessages = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->name,
                'user_avatar' => $message->user->avatar ?? null,
                'message' => $message->message,
                'created_at' => $message->created_at->format('M j, Y g:i A'),
                'file_path' => $message->file_path,
                'file_name' => $message->file_path ? basename($message->file_path) : null,
                'chat_type' => $message->chat_type,
            ];
        });

        return response()->json(['messages' => $formattedMessages]);
    }

    /**
     * Get unread messages count for a specific pengajuan
     */
    public function getUnreadCount($pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        // Determine chat_type based on user role and pengajuan status
        $chatType = 'verifikator';
        if ($user->role === 'pokjapemilihan') {
            $chatType = 'pokja';
        } elseif ($user->role === 'ppk') {
            $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
        }

        // Count unread messages
        $unreadCount = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', $chatType)
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread_count' => $unreadCount]);
    }

    /**
     * Mark messages as read
     */
    public function markAsRead($pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);

        // Determine chat_type based on user role and pengajuan status
        $chatType = 'verifikator';
        if ($user->role === 'pokjapemilihan') {
            $chatType = 'pokja';
        } elseif ($user->role === 'ppk') {
            $chatType = $pengajuan->status < 20 ? 'verifikator' : 'pokja';
        }

        // Mark all unread messages as read
        \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->where('chat_type', $chatType)
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
