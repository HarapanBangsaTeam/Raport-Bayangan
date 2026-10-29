<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Tahun Pelajaran & Semester') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Atur periode aktif UTS (Semester 1 / 2) serta simpan seluruh arsip riwayat nilai di SQLite tanpa tertimpa.
                </p>
            </div>
            @if($activeYear)
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300 shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Periode Aktif: T.A. {{ $activeYear->name }} - Semester {{ $activeYear->semester }} ({{ $activeYear->semester_name }})</span>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2 text-sm font-semibold">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- PANDUAN PENTING DARI SISTEM -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 shadow-sm text-indigo-900">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg text-indigo-700 shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="space-y-1 text-sm">
                        <h4 class="font-bold text-base text-indigo-950">Cara Kerja Semester & Arsip Nilai (Database SQLite Aman)</h4>
                        <p class="text-xs text-indigo-800 leading-relaxed">
                            Setiap nilai dan absensi otomatis terhubung ke Tahun Pelajaran & Semester yang aktif.
                            Ketika beralih dari <strong>Semester 1 ke Semester 2</strong>, Anda cukup klik tombol <strong>"Aktifkan"</strong> pada Semester 2. 
                            Halaman <em>Input Nilai</em> otomatis siap untuk nilai baru tanpa perlu menghapus nilai Semester 1 secara manual. Seluruh riwayat UTS Semester 1 tetap aman dan dapat dicetak kembali kapan pun.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- FORM TAMBAH PERIODE BARU -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 h-fit">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 bg-blue-50 text-blue-700 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <h3 class="font-bold text-base text-gray-900">Tambah Periode Baru</h3>
                    </div>

                    <form action="{{ route('academic-years.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 mb-1">
                                Tahun Pelajaran <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required placeholder="Contoh: 2026/2027 atau 2027/2028"
                                value="{{ old('name', '2026/2027') }}"
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                            @error('name')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-gray-500 mt-1">Format tahun pelajaran: YYYY/YYYY</p>
                        </div>

                        <div>
                            <label for="semester" class="block text-xs font-bold text-gray-700 mb-1">
                                Semester <span class="text-rose-500">*</span>
                            </label>
                            <select name="semester" id="semester" required
                                class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('semester') border-rose-500 @enderror">
                                <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                                <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>Semester 2 (Genap)</option>
                            </select>
                            @error('semester')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-semibold text-gray-700">Langsung aktifkan periode ini</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg text-sm shadow-md transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Periode</span>
                        </button>
                    </form>
                </div>

                <!-- DAFTAR TAHUN PELAJARAN -->
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-base text-gray-900">Riwayat Tahun Pelajaran & Semester</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih salah satu periode untuk dijadikan periode aktif pengisian nilai dan raport.</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md">
                            Total: {{ $academicYears->count() }} Periode
                        </span>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse($academicYears as $year)
                        <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors {{ $year->is_active ? 'bg-blue-50/50 border-l-4 border-l-blue-600' : '' }}">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-base text-gray-900">
                                        T.A. {{ $year->name }}
                                    </h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $year->semester == 1 ? 'bg-amber-100 text-amber-900' : 'bg-purple-100 text-purple-900' }}">
                                        Semester {{ $year->semester }} ({{ $year->semester_name }})
                                    </span>
                                    @if($year->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-xs font-black bg-emerald-600 text-white shadow-sm flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        SEDANG AKTIF
                                    </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Arsip Nilai: <strong class="text-gray-800">{{ $year->grades_count }}</strong> data nilai
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Absensi: <strong class="text-gray-800">{{ $year->attendances_count }}</strong> siswa
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                @if(!$year->is_active)
                                <form action="{{ route('academic-years.set-active', $year->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition-all"
                                        title="Jadikan periode ini aktif untuk input nilai dan cetak raport">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Aktifkan Periode Ini
                                    </button>
                                </form>
                                @endif

                                <a href="{{ route('academic-years.edit', $year->id) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold border border-gray-300 transition-all"
                                    title="Edit Nama Tahun atau Semester">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>

                                @if(!$year->is_active && $year->grades_count == 0)
                                <form action="{{ route('academic-years.destroy', $year->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus periode T.A. {{ $year->name }} Semester {{ $year->semester }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-bold border border-rose-200 transition-all"
                                        title="Hapus periode kosong ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-500 text-sm">
                            Belum ada tahun pelajaran yang didaftarkan. Silakan tambahkan pada form di samping.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
