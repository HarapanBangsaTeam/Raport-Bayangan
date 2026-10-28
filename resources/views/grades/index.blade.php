<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Daftar Siswa & Pengisian Nilai') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola dan perbarui nilai harian (PR, UH) serta sumatif (Tertulis, Praktek) per siswa.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                    Total Siswa: {{ $students->total() }}
                </span>

                @if(($totalGradesCount ?? 0) > 0)
                    <button type="button" 
                            x-data="" 
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-all-grades')" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-bold shadow-xs transition-all focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Kosongkan Semua Nilai</span>
                    </button>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200" title="Belum ada data nilai yang diinput">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Nilai Kosong</span>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-600 p-4 rounded-xl shadow-xs">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-emerald-600">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            <!-- Tab Navigasi Mode Input Nilai -->
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
                <a href="{{ route('grades.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-blue-700 text-white shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Mode per Siswa</span>
                </a>
                <a href="{{ route('grades.by-subject') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-gray-700 hover:bg-gray-100 border border-gray-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Mode per Mata Pelajaran (Cepat)</span>
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-3.5 text-center w-12">No</th>
                                <th class="px-6 py-3.5 text-left">Nama Siswa</th>
                                <th class="px-6 py-3.5 text-left">NISN / NIS</th>
                                <th class="px-6 py-3.5 text-center">Kelas</th>
                                <th class="px-6 py-3.5 text-center">Status Kelengkapan</th>
                                <th class="px-6 py-3.5 text-center">Aksi Pengisian Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($students as $index => $student)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600 font-semibold">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                    {{ $student->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-mono text-xs font-medium">
                                    {{ $student->nisn ? $student->nisn : '-' }} / {{ $student->nis ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                                        Kelas {{ $student->classroom->name ?? 'VII' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $filledCount = $student->grades->filter(function ($g) {
                                            return $g->pr !== null || $g->uh !== null || $g->tertulis !== null || $g->praktek !== null;
                                        })->count();
                                        $totalMapel = $activeSubjectsCount ?? 1;
                                    @endphp
                                    @if($filledCount === 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Belum Diisi
                                        </span>
                                    @elseif($filledCount >= $totalMapel && $totalMapel > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Lengkap ({{ $filledCount }}/{{ $totalMapel }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Sebagian ({{ $filledCount }}/{{ $totalMapel }})
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('grades.edit', $student->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            Input / Edit Nilai
                                        </a>
                                        <a href="{{ route('reports.preview', $student->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat Raport
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $students->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kosongkan Semua Nilai -->
    <x-modal name="confirm-delete-all-grades" focusable>
        <form method="POST" action="{{ route('grades.destroy-all') }}" class="p-6" x-data="{ confirmation: '', resetAttendance: true }">
            @csrf
            @method('DELETE')

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-900 leading-snug">
                        Konfirmasi Kosongkan Semua Nilai Siswa
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Tindakan ini akan menghapus seluruh data nilai mata pelajaran (PR, UH, Tertulis, Praktek) yang telah dimasukkan untuk semua siswa pada tahun ajaran aktif 
                        @if($academicYear)
                            <span class="font-bold text-gray-800">({{ $academicYear->name }} - Semester {{ $academicYear->semester }})</span>
                        @endif.
                    </p>
                    <div class="mt-2.5 text-xs text-rose-800 font-medium bg-rose-50 border border-rose-200 rounded-lg p-3">
                        <span class="font-bold">Tujuan fitur:</span> Mempermudah guru saat memasuki semester baru (misal semester 2) sehingga guru dapat langsung memasukkan nilai baru tanpa harus menghapus nilai masing-masing siswa satu per satu.
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 space-y-4">
                <label class="flex items-center gap-3 cursor-pointer select-none bg-slate-50 border border-gray-200 p-3 rounded-lg hover:bg-slate-100 transition-colors">
                    <input type="checkbox" name="reset_attendance" value="1" x-model="resetAttendance" class="w-4 h-4 rounded text-blue-700 border-gray-300 focus:ring-blue-500">
                    <span class="text-xs sm:text-sm text-gray-700 font-medium">
                        Reset juga data absensi (Sakit, Izin, Alpa) dan catatan wali kelas ke kondisi awal
                    </span>
                </label>

                <div>
                    <label for="confirm_delete_all" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Ketik kata <span class="text-rose-700 font-mono font-bold">HAPUS</span> di bawah ini untuk mengonfirmasi:
                    </label>
                    <input
                        id="confirm_delete_all"
                        type="text"
                        x-model="confirmation"
                        autocomplete="off"
                        placeholder="Ketik HAPUS"
                        class="block w-full text-sm rounded-lg border-gray-300 shadow-xs focus:border-rose-500 focus:ring-rose-500"
                    />
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <button
                    type="submit"
                    :disabled="confirmation.trim().toUpperCase() !== 'HAPUS'"
                    class="inline-flex items-center px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-bold shadow-xs transition-all disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                >
                    Ya, Kosongkan Semua Nilai
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

