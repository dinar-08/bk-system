<?php

namespace App\Notifications;

use App\Models\Pemanggilan;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class JadwalPemanggilanNotification extends Notification
{
    use Queueable;

    public function __construct(public Pemanggilan $pemanggilan, public bool $isReschedule = false)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        $laporan = $this->pemanggilan->laporan;
        $judul = $this->isReschedule ? 'Jadwal Pemanggilan Dirubah' : 'Jadwal Pemanggilan Baru';

        return [
            'laporan_id' => $laporan->laporan_id,
            'judul' => $judul,
            'pesan' => "Anak Anda ({$laporan->siswa->nama_siswa}) dijadwalkan dipanggil pada {$this->tanggalFormat()} pukul {$this->waktuFormat()} terkait laporan: {$laporan->judul_laporan}",
            'url' => route('orang_tua.laporan.index'),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        $laporan = $this->pemanggilan->laporan;
        $judul = $this->isReschedule ? 'Jadwal Pemanggilan Dirubah' : 'Jadwal Pemanggilan Baru';

        return (new WebPushMessage)
            ->title($judul)
            ->icon('/asset/logo.png')
            ->body("{$laporan->siswa->nama_siswa} dijadwalkan dipanggil BK pada {$this->tanggalFormat()} pukul {$this->waktuFormat()} terkait: {$laporan->judul_laporan}.")
            ->action('Lihat Detail', 'lihat')
            ->data(['url' => route('orang_tua.laporan.index')]);
    }

    /**
     * 
     */
    protected function tanggalFormat(): string
    {
        return Carbon::parse($this->pemanggilan->tanggal_pemanggilan)
            ->translatedFormat('d F Y');
    }

    /**
     * 
     */
    protected function waktuFormat(): string
    {
        return Carbon::parse($this->pemanggilan->waktu_pemanggilan)
            ->format('H:i');
    }
}