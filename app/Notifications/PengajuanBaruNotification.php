<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengajuanBaruNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $pengajuan;
    public $pesan;

    public function __construct($pengajuan, $pesan)
    {
        $this->pengajuan = $pengajuan;
        $this->pesan = $pesan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        return [
            'judul' => 'Pengajuan Baru Masuk',
            'pesan' => $this->pesan,
            'url' => route('verifikator_pengajuanopen', $this->pengajuan->id),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
