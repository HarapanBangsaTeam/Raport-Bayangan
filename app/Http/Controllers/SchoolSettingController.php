<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\Http\Request;

class SchoolSettingController extends Controller
{
    public function edit()
    {
        $school = SchoolSetting::firstOrCreate([], [
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => null,
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);

        $dbPath = config('database.connections.sqlite.database');
        $dbExists = is_string($dbPath) && file_exists($dbPath);
        $dbSizeFormatted = $dbExists ? number_format(filesize($dbPath) / 1024, 1).' KB' : '0 KB';
        $dbLastModified = $dbExists ? date('d M Y, H:i', filemtime($dbPath)) : '-';

        return view('settings.school', compact('school', 'dbExists', 'dbSizeFormatted', 'dbLastModified'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'judul_laporan' => 'required|string|max:255',
            'nama_kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:100',
            'nama_wali_kelas' => 'nullable|string|max:255',
            'nip_wali_kelas' => 'nullable|string|max:100',
            'tanggal_raport' => 'nullable|string|max:100',
        ]);

        $school = SchoolSetting::first();
        if ($school) {
            $school->update($validated);
        } else {
            SchoolSetting::create($validated);
        }

        return redirect()->route('settings.school')->with('success', 'Pengaturan Kop Surat, Nama Kegiatan, dan Tanda Tangan berhasil disimpan.');
    }
}
