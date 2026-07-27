<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $primaryKey = 'nis';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nis',
        'user_id',
        'nama_siswa',
        'kelas',
        'tahun_ajaran',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat',
        'nama_ortu',
        'no_whatsapp',
        'foto',
        'last_data_updated_at',
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
        return $this->hasMany(Laporan::class, 'nis', 'nis');
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
