<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;

class ArsipController extends Controller
{
    public function index()
    {
        $arsip = Siswa::with('user')
            ->whereHas('user', function ($query) {
                $query->where('status_akun', 'nonaktif');
            })
            ->latest()
            ->get();

        return view('admin.arsip.index', compact('arsip'));
    }

    public function show($id)
    {
        $siswa = Siswa::with([
            'user',
            'laporan.pemanggilan',
            'laporan.monitoring',
            'laporan.evaluasi'
        ])->findOrFail($id);

        return view('admin.arsip.show', compact('siswa'));
    }
}