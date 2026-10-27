<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Raport Bayangan') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">SMP Harapan Bangsa Karawang</p>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                T.A. {{ $activeYear->name ?? '2026/2027' }} - Semester {{ $activeYear->semester ?? 1 }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistik Ringkas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6 flex items-center justify-between">
                    <div>
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Siswa</div>
                        <div class="text-3xl font-extrabold text-blue-800 mt-1">{{ $totalStudents }}</div>
                        <div class="text-xs text-gray-500 mt-1">Kelas VII Aktif</div>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-700 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6 flex items-center justify-between">
                    <div>
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Mata Pelajaran</div>
                        <div class="text-3xl font-extrabold text-emerald-800 mt-1">{{ $totalSubjects }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $totalSubjects }} Mapel Aktif</div>
                    </div>
                    <div class="p-3 bg-emerald-50 text-emerald-700 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6 flex items-center justify-between">
                    <div>
                        <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Tahun Pelajaran</div>
                        <div class="text-2xl font-extrabold text-indigo-800 mt-1">{{ $activeYear->name ?? '2026/2027' }}</div>
                        <div class="text-xs text-gray-500 mt-1">Semester {{ $activeYear->semester ?? 1 }} (Ganjil)</div>
                    </div>
                    <div class="p-3 bg-indigo-50 text-indigo-700 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Akses Cepat (Quick Actions) -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Aksi Cepat Menu Utama</h3>
                <p class="text-sm text-gray-600 mb-6">Pilih menu di bawah ini untuk mengelola nilai atau langsung mencetak raport bayangan siswa.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <a href="{{ route('grades.index') }}" class="flex items-center gap-3 p-4 bg-blue-700 hover:bg-blue-800 text-white rounded-xl shadow-md transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <div class="text-base font-bold">Input & Edit Nilai</div>
                            <div class="text-xs text-blue-100 font-normal">Perbarui PR, UH, Tertulis & Praktek</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 p-4 bg-indigo-700 hover:bg-indigo-800 text-white rounded-xl shadow-md transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <div class="text-base font-bold">Daftar Raport Siswa</div>
                            <div class="text-xs text-indigo-100 font-normal">Preview atau unduh Excel per siswa</div>
                        </div>
                    </a>

                    <a href="{{ route('reports.print-all') }}" class="flex items-center gap-3 p-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl shadow-md transition-all font-semibold">
                        <div class="p-2.5 bg-white/10 rounded-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </div>
                        <div>
                            <div class="text-base font-bold">Cetak Semua Raport</div>
                            <div class="text-xs text-emerald-100 font-normal">Cetak 1 kelas sekaligus (A4 Page Break)</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
