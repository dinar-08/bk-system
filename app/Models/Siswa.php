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
        'tahun_ajaran',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
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
        if (!$this->last_data_updated_at) {
            return false;
        }

        return $this->last_data_updated_at->between(
            $periode->tanggal_mulai->copy()->startOfDay(),
            $periode->tanggal_selesai->copy()->endOfDay()
        );
    }

    public function tandaiSudahUpdate(PeriodeUpdate $periode): void
    {
        $this->update([
            'last_data_updated_at' => now(),
        ]);
    }
}