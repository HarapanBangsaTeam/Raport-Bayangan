<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-gray-900 leading-tight">
                    {{ __('Pengaturan Kop Surat & Header Raport') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Ubah nama sekolah, alamat, judul kegiatan (misal: UTS, PTS, PAS), tanggal, dan nama penandatangan raport.
                </p>
            </div>
            <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-lg shadow-sm text-xs transition-all">
                Kembali ke Raport
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded-xl shadow-sm">
                    <ul class="text-sm font-semibold text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('settings.school.update') }}" method="POST" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200 p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- SECTION 1: KOP SURAT & NAMA KEGIATAN -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-800 text-xs flex items-center justify-center font-bold">1</span>
                        Kop Surat & Judul Kegiatan
                    </h3>
                    <p class="text-xs text-gray-500 mb-4 ml-8">Informasi yang tercetak di area paling atas (header) lembar raport.</p>

                    <div class="grid grid-cols-1 gap-4 ml-8">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Sekolah</label>
                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $school->nama_sekolah) }}" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Alamat Sekolah</label>
                            <input type="text" name="alamat" value="{{ old('alamat', $school->alamat) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-blue-900 mb-1">
                                Judul Kegiatan Raport (Contoh: Laporan Hasil Belajar Tengah Semester 1 / Raport Bayangan / PAS)
                            </label>
                            <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $school->judul_laporan ?? 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)') }}" required
                                class="w-full rounded-lg border-blue-400 bg-blue-50/40 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-bold text-gray-900">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: TANDA TANGAN & TANGGAL -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-base font-extrabold text-gray-900 mb-1 flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-xs flex items-center justify-center font-bold">2</span>
                        Tanggal Raport & Penandatangan
                    </h3>
                    <p class="text-xs text-gray-500 mb-4 ml-8">Informasi kota/tanggal dan nama pejabat yang tertera pada bagian tanda tangan bawah.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 ml-8">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Kota & Tanggal Raport (Contoh: Karawang, 02 Oktober 2026)</label>
                            <input type="text" name="tanggal_raport" value="{{ old('tanggal_raport', $school->tanggal_raport ?? 'Karawang, 02 Oktober 2026') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Kepala Sekolah</label>
                            <input type="text" name="nama_kepala_sekolah" value="{{ old('nama_kepala_sekolah', $school->nama_kepala_sekolah) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">NIPY / NIP Kepala Sekolah</label>
                            <input type="text" name="nip_kepala_sekolah" value="{{ old('nip_kepala_sekolah', $school->nip_kepala_sekolah) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Wali Kelas</label>
                            <input type="text" name="nama_wali_kelas" value="{{ old('nama_wali_kelas', $school->nama_wali_kelas) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">NIPY / NIP Wali Kelas</label>
                            <input type="text" name="nip_wali_kelas" value="{{ old('nip_wali_kelas', $school->nip_wali_kelas) }}" placeholder="Boleh dikosongkan (-)"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 text-sm font-mono">
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN -->
                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('reports.index') }}" class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-xl shadow-sm text-sm transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl shadow-md text-sm flex items-center gap-2 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <!-- SECTION PENCADANGAN & PEMULIHAN DATABASE (BACKUP & RESTORE) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200 p-6 sm:p-8 space-y-6">
                <div class="border-b border-gray-200 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                            <span class="p-1.5 bg-emerald-100 text-emerald-800 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                            </span>
                            <span>Cadangan & Pemulihan Database (Backup & Restore)</span>
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Seluruh data siswa, riwayat nilai semester 1 dan 2, absensi, serta kop surat tersimpan di berkas SQLite. Unduh berkas cadangan secara berkala untuk disimpan di komputer atau flashdisk.
                        </p>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 text-slate-800 rounded-lg text-xs font-semibold shrink-0">
                        <span>Ukuran: <strong>{{ $dbSizeFormatted }}</strong></span>
                        <span class="text-gray-400">|</span>
                        <span>Update: <strong>{{ $dbLastModified }}</strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- KARTU 1: DOWNLOAD BACKUP -->
                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl flex flex-col justify-between space-y-4">
                        <div class="space-y-1.5">
                            <h4 class="font-bold text-sm text-gray-900 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>Unduh Berkas Cadangan</span>
                            </h4>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Simpan salinan database lengkap ke laptop Anda saat ini. Sangat disarankan sebelum mengosongkan nilai atau setelah pembagian raport tengah semester.
                            </p>
                        </div>

                        <a href="{{ route('settings.backup.download') }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Download Backup Database (.sqlite)</span>
                        </a>
                    </div>

                    <!-- KARTU 2: RESTORE DARI FILE BACKUP -->
                    <div class="p-5 bg-amber-50/50 border border-amber-200 rounded-xl flex flex-col justify-between space-y-4">
                        <div class="space-y-1.5">
                            <h4 class="font-bold text-sm text-amber-950 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Pulihkan dari File Cadangan (Restore)</span>
                            </h4>
                            <p class="text-xs text-amber-900/80 leading-relaxed">
                                Unggah berkas cadangan (.sqlite) yang pernah Anda unduh untuk mengembalikan seluruh data ke kondisi pada saat cadangan dibuat. Sistem otomatis membuat checkpoint darurat sebelum data ditimpa.
                            </p>
                        </div>

                        <form action="{{ route('settings.backup.restore') }}" method="POST" enctype="multipart/form-data"
                            onsubmit="return confirm('PENTING: Apakah Anda yakin ingin memulihkan database dari file cadangan ini? Data saat ini akan digantikan oleh isi file cadangan.');"
                            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            @csrf
                            <input type="file" name="backup_file" required accept=".sqlite,.sqlite3,.db,.bak"
                                class="block w-full text-xs text-gray-700 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200">
                            <button type="submit"
                                class="px-4 py-2 bg-amber-700 hover:bg-amber-800 text-white rounded-xl text-xs font-bold shadow-md transition-all whitespace-nowrap shrink-0">
                                Pulihkan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
