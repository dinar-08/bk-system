<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class MonitoringBaruNotification extends Notification
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
            'judul' => 'Monitoring Baru',
            'pesan' => "Ada update monitoring baru untuk laporan: {$this->laporan->judul_laporan}",
            'url' => route('orang_tua.perkembangan.show', $this->laporan->laporan_id),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Monitoring Baru')
            ->icon('/asset/logo.png')
            ->body("Ada update monitoring baru untuk laporan: {$this->laporan->judul_laporan}")
            ->action('Lihat Detail', 'lihat')
            ->data(['url' => route('orang_tua.perkembangan.show', $this->laporan->laporan_id)]);
    }
}