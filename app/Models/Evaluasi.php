<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    protected $table = 'evaluasi';

    protected $fillable = [
        'laporan_id',
        'guru_bk_id',
        'tanggal_evaluasi',
        'hasil_evaluasi',
        'status_akhir',
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