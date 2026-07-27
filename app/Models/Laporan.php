<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $primaryKey = 'laporan_id';

    protected $fillable = [
        'nis',
        'nip',
        'judul_laporan',
        'kategori',
        'jenis_masalah',
        'deskripsi',
        'bukti',
        'status',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'nis', 'nis');
    }

    public function guruBk()
    {
        return $this->belongsTo(GuruBK::class, 'nip', 'nip');
    }

    public function pemanggilan()
    {
        return $this->hasMany(Pemanggilan::class, 'laporan_id', 'laporan_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'laporan_id', 'laporan_id');
    }

    public function evaluasi()
    {
        return $this->hasOne(Evaluasi::class, 'laporan_id', 'laporan_id');
    }
}