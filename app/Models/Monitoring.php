<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $table = 'monitoring';

    protected $primaryKey = 'monitoring_id';

    protected $fillable = [
        'laporan_id',
        'nip',
        'tanggal_monitoring',
        'waktu_monitoring',
        'tanggal_monitoring_berikutnya',
        'waktu_monitoring_berikutnya',
        'monitoring_ke',
        'status_monitoring',
        'status_perkembangan',
        'catatan_perkembangan',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id', 'laporan_id');
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'nip', 'nip');
    }
}