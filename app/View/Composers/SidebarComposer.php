<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatConversation;
use App\Models\ChatMessage;

class SidebarComposer
{
    public function compose(View $view)
    {
        $unreadCount = 0;
        
        if (Auth::check()) {
            $user = Auth::user();
            
            // Hanya hitung untuk PPK dan Pokja yang punya akses chat
            if (in_array($user->role, ['ppk', 'pokjapemilihan'])) {
                // Ambil semua conversation user ini
                $conversationIds = ChatConversation::whereJsonContains('participants', $user->id)
                    ->pluck('id');
                
                // Hitung pesan yang belum dibaca (dari user lain)
                $unreadCount = ChatMessage::whereIn('conversation_id', $conversationIds)
                    ->where('user_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->count();
            }
        }
        
        $view->with('unreadChatCount', $unreadCount);
    }
}
