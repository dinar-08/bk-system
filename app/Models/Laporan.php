<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = [
        'siswa_id',
        'guru_bk_id',
        'judul_laporan',
        'kategori',
        'jenis_masalah',
        'deskripsi',
        'bukti',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'guru_bk_id');
    }

    public function pemanggilan()
    {
        return $this->hasMany(Pemanggilan::class);
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class);
    }

    public function evaluasi()
    {
        return $this->hasOne(Evaluasi::class);
    }
}