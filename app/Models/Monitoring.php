<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $table = 'monitoring';

    protected $fillable = [
        'laporan_id',
        'guru_bk_id',
        'tanggal_monitoring',
        'waktu_monitoring',
        'monitoring_ke',
        'status_perkembangan',
        'catatan_perkembangan',
        'tanggal_monitoring_berikutnya',
        'waktu_monitoring_berikutnya',
        'status_monitoring',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class);
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'guru_bk_id');
    }
}