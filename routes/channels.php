<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatConversation;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Chat channels - only participants can listen
Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    Log::info('Chat channel authorization attempt', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'user_name' => $user->name
    ]);
    
    $conversation = ChatConversation::find($conversationId);
    $hasAccess = $conversation && $conversation->hasParticipant($user->id);
    
    Log::info('Chat channel authorization result', [
        'user_id' => $user->id,
        'conversation_id' => $conversationId,
        'conversation_found' => $conversation ? true : false,
        'participants' => $conversation ? $conversation->participants : null,
        'has_access' => $hasAccess
    ]);
    
    return $hasAccess;
});
