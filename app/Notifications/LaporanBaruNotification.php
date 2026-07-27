<?php

namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class LaporanBaruNotification extends Notification
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
            'judul' => 'Laporan Baru',
            'pesan' => "Anak Anda ({$this->laporan->siswa->nama_siswa}) mendapat laporan baru: {$this->laporan->judul_laporan}",
            'url' => route('orang_tua.laporan.index'),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Laporan Baru')
            ->icon('/asset/logo.png')
            ->body("Anak Anda mendapat laporan baru: {$this->laporan->judul_laporan}")
            ->action('Lihat Detail', 'lihat')
            ->data(['url' => route('orang_tua.laporan.index')]);
    }
}