<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class EvaluasiBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public Laporan $laporan)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'laporan_id' => $this->laporan->laporan_id,
            'judul' => 'Evaluasi Selesai',
            'pesan' => "Evaluasi untuk laporan \"{$this->laporan->judul_laporan}\" sudah selesai.",
            'url' => route('orang_tua.laporan.index'),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Evaluasi Selesai')
            ->icon('/asset/logo.png')
            ->body("Evaluasi untuk laporan \"{$this->laporan->judul_laporan}\" sudah selesai.")
            ->action('Lihat Detail', 'lihat')
            ->data(['url' => route('orang_tua.laporan.index')]);
    }
}