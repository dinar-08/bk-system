<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemanggilan extends Model
{
    protected $table = 'pemanggilan';

    protected $fillable = [
        'laporan_id',
        'tanggal_pemanggilan',
        'waktu_pemanggilan',
        'pihak_dipanggil',
        'tujuan',
        'status_kehadiran',
        'tindak_lanjut',
        'tanggal_monitoring',
        'catatan',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }
}