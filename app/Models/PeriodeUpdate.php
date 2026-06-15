<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeUpdate extends Model
{
    protected $table = 'periode_update';

    protected $fillable = [
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'aktif' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function aktifSekarang()
    {
        return static::where('aktif', true)
            ->where('tanggal_mulai', '<=', now()->toDateString())
            ->where('tanggal_selesai', '>=', now()->toDateString())
            ->first();
    }
}