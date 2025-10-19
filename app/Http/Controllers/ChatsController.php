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

        // Restrict access to PPK and Pokja Pemilihan only
        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan'])) {
            abort(403, 'Akses ditolak. Fitur chat hanya tersedia untuk PPK dan Pokja Pemilihan.');
        }

        // Get user info for chat context
        $user = Auth::user();
        $userRole = $user->role;
        $userName = $user->name;

        // Get pengajuan data
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);
        
        // Determine return route based on user role
        $returnRoute = $userRole === 'ppk' 
            ? route('ppk_pengajuanopen', ['id' => $pengajuanId])
            : route('pokjapemilihan_pengajuanopen', ['id' => $pengajuanId]);
        
        return view('chatsnew', compact('userRole', 'userName', 'pengajuan', 'returnRoute'));
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

    public function sendMessage(Request $request, $pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan'])) {
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

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Store file in public/chat_uploads directory
            $file->storeAs('public/chat_uploads', $fileName);
            $filePath = '/storage/chat_uploads/' . $fileName;
        }

        // Create chat message
        $message = new \App\Models\ChatMessage([
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'file_path' => $filePath
        ]);
        $message->save();

        // Format response
        $response = [
            'id' => $message->id,
            'sender_name' => $user->name,
            'sender_initial' => strtoupper(substr($user->name, 0, 1)),
            'message' => $message->message,
            'time' => $message->created_at->format('M j, Y g:i A'),
            'file_path' => $filePath,
            'file_name' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null
        ];

        return response()->json($response);
    }

    public function getNewMessages($pengajuanId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array(Auth::user()->role, ['ppk', 'pokjapemilihan'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Ensure pengajuan exists and user has access
        $pengajuan = \App\Models\Pengajuan::findOrFail($pengajuanId);
        
        // Get messages for this pengajuan
        $messages = \App\Models\ChatMessage::where('pengajuan_id', $pengajuanId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $comments = $messages->map(function($message) {
            return [
                'id' => $message->id,
                'sender_name' => $message->user->name,
                'sender_initial' => strtoupper(substr($message->user->name, 0, 1)),
                'message' => $message->message,
                'time' => $message->created_at->format('M j, Y g:i A'),
                'file_path' => $message->file_path,
                'file_name' => $message->file_path ? basename($message->file_path) : null
            ];
        });

        return response()->json(['comments' => $comments]);
    }
}
