<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-chat-connection', function () {
    return view('test-chat-connection');
})->middleware('auth');

Route::get('/test-broadcast', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    // Test basic broadcast functionality
    return response()->json([
        'user' => auth()->user()->only(['id', 'name', 'role']),
        'broadcast_driver' => config('broadcasting.default'),
        'reverb_config' => [
            'app_key' => config('broadcasting.connections.reverb.key'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
        ]
    ]);
});

Route::post('/test-send-message', function () {
    if (!auth()->check()) {
        return response()->json(['error' => 'Not authenticated']);
    }
    
    try {
        // Create a test chat message
        $message = \App\Models\ChatMessage::create([
            'conversation_id' => 1, // Assuming conversation ID 1 exists
            'user_id' => auth()->id(),
            'message' => 'Test message from ' . auth()->user()->name,
            'type' => 'text'
        ]);
        
        // Fire the event
        event(new \App\Events\MessageSent($message));
        
        return response()->json([
            'success' => true,
            'message' => 'Message sent and event fired',
            'data' => $message
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ]);
    }
})->middleware('auth');
