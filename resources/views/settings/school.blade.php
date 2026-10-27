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

        </div>
    </div>
</x-app-layout>
