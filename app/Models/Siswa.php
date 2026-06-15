<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nama_siswa',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'kelas',
        'nama_ortu',
        'no_whatsapp',
        'foto',
        'last_data_updated_at',
    ];

    protected $casts = [
        'last_data_updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    public function sudahUpdateDiPeriode(PeriodeUpdate $periode): bool
    {
        return $this->last_data_updated_at &&
            $this->last_data_updated_at->gte($periode->tanggal_mulai);
    }
}