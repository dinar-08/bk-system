<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruBK extends Model
{
    protected $table = 'guru_bk';

    protected $primaryKey = 'nip';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nip',
        'user_id',
        'nama',
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
        return $this->hasMany(Laporan::class, 'nip', 'nip');
    }

    public function pemanggilan()
    {
        return $this->hasMany(Pemanggilan::class, 'nip', 'nip');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'nip', 'nip');
    }

    public function evaluasi()
    {
        return $this->hasMany(Evaluasi::class, 'nip', 'nip');
    }
}
