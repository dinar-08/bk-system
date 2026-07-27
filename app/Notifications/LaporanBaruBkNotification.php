<?php
namespace App\Notifications;

use App\Models\Laporan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class LaporanBaruBkNotification extends Notification
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
            'judul' => 'Laporan Baru Masuk',
            'pesan' => "Laporan baru dari {$this->laporan->siswa->nama_siswa}: {$this->laporan->judul_laporan}",
            'url' => route('bk.laporan.show', $this->laporan->laporan_id),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Laporan Baru Masuk')
            ->icon('/asset/logo.png')
            ->body("Laporan baru dari {$this->laporan->siswa->nama_siswa}: {$this->laporan->judul_laporan}")
            ->action('Lihat Detail', 'lihat')
            ->data(['url' => route('bk.laporan.show', $this->laporan->laporan_id)]);
    }
}