<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruBK extends Model
{
    protected $table = 'guru_bk';

    protected $fillable = [
        'user_id',
        'nama',
        'nip',
        'no_hp',
        'alamat',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'guru_bk_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'guru_bk_id');
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class, 'guru_bk_id');
    }
}