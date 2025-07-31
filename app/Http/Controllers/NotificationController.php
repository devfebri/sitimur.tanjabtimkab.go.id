<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function bacaSemua()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('pesan', 'Semua notifikasi telah dibaca.');
    }
    public function bacaNotif($id)
    {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        return redirect($notif->data['url']);
    }
}
