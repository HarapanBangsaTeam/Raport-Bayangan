<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                    {{ __('Input Nilai: ') }} {{ $student->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    Kelas {{ $student->classroom->name ?? '-' }} | NISN: {{ $student->nisn ?? '-' }} | NIS: {{ $student->nis ?? '-' }}
                </p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                @if($prevStudent)
                    <a href="{{ route('grades.edit', $prevStudent->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-300 shadow-2xs transition-all" title="Pindah ke {{ $prevStudent->name }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Siswa Sebelumnya</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg border border-gray-200 cursor-not-allowed">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Siswa Sebelumnya</span>
                    </span>
                @endif

                @if($nextStudent)
                    <a href="{{ route('grades.edit', $nextStudent->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white hover:bg-gray-100 text-gray-700 text-xs font-bold rounded-lg border border-gray-300 shadow-2xs transition-all" title="Pindah ke {{ $nextStudent->name }}">
                        <span>Siswa Berikutnya</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg border border-gray-200 cursor-not-allowed">
                        <span>Siswa Berikutnya</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <form action="{{ route('grades.store', $student->id) }}" method="POST">
                    @csrf
                    
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th rowspan="2" class="px-3 py-3 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                    <th rowspan="2" class="px-3 py-3 border border-gray-300 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th colspan="2" class="px-3 py-2 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Formative Harian</th>
                                    <th colspan="2" class="px-3 py-2 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sumatif</th>
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PR</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UH</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tertulis</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Praktek</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subjects as $index => $subject)
                                    @php
                                        $grade = $grades->get($subject->id);
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-2 border border-gray-300 text-center text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 border border-gray-300 text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][pr]" value="{{ old('grades.'.$subject->id.'.pr', $grade->pr ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][uh]" value="{{ old('grades.'.$subject->id.'.uh', $grade->uh ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][tertulis]" value="{{ old('grades.'.$subject->id.'.tertulis', $grade->tertulis ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][praktek]" value="{{ old('grades.'.$subject->id.'.praktek', $grade->praktek ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="font-bold text-lg mb-2">Keterangan Kehadiran</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Sakit</label>
                                <input type="number" min="0" name="attendance[sakit]" value="{{ old('attendance.sakit', $attendance->sakit ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Izin</label>
                                <input type="number" min="0" name="attendance[izin]" value="{{ old('attendance.izin', $attendance->izin ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Alpa</label>
                                <input type="number" min="0" name="attendance[alpa]" value="{{ old('attendance.alpa', $attendance->alpa ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div x-data="{
                            note: @js(old('attendance.note', $attendance->note ?? 'Tetap semangat belajar ya nak!')),
                            setNote(text) {
                                this.note = text;
                            }
                        }">
                            <h3 class="font-bold text-lg mb-2 text-gray-800">Catatan Raport</h3>
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Wali Kelas</label>
                                <textarea name="attendance[note]" x-model="note" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"></textarea>
                            </div>

                            <div>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1.5">Pilihan Cepat Template Catatan:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" @click="setNote('Prestasi sangat baik, pertahankan semangat belajarmu!')" class="text-xs px-2.5 py-1 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 rounded-md border border-slate-200 transition-colors text-left">
                                        Prestasi sangat baik
                                    </button>
                                    <button type="button" @click="setNote('Tingkatkan ketekunan dalam mengerjakan tugas harian dan PR.')" class="text-xs px-2.5 py-1 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 rounded-md border border-slate-200 transition-colors text-left">
                                        Tingkatkan ketekunan PR
                                    </button>
                                    <button type="button" @click="setNote('Lebih aktif dan fokus selama pembelajaran di kelas.')" class="text-xs px-2.5 py-1 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 rounded-md border border-slate-200 transition-colors text-left">
                                        Lebih aktif & fokus
                                    </button>
                                    <button type="button" @click="setNote('Tingkatkan kehadiran dan kedisiplinan belajar di sekolah.')" class="text-xs px-2.5 py-1 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 rounded-md border border-slate-200 transition-colors text-left">
                                        Disiplin kehadiran
                                    </button>
                                    <button type="button" @click="setNote('Tetap semangat belajar ya nak!')" class="text-xs px-2.5 py-1 bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-800 rounded-md border border-slate-200 transition-colors text-left">
                                        Default penyemangat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-200 pt-6">
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                            <a href="{{ route('grades.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-lg shadow-sm text-xs transition-all">
                                Kembali ke Daftar Nilai
                            </a>
                            <button type="button" 
                                    x-data="" 
                                    x-on:click.prevent="$dispatch('open-modal', 'confirm-delete-student-grades')" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold rounded-lg text-xs shadow-xs transition-all">
                                <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Kosongkan Nilai Siswa Ini</span>
                            </button>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
                            <button type="reset" class="px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-lg shadow-sm text-xs transition-all">
                                Reset
                            </button>
                            <button type="submit" name="action" value="save" class="px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg shadow-md text-xs transition-all">
                                Simpan Nilai
                            </button>
                            <button type="submit" name="action" value="save_and_next" class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-700 hover:bg-indigo-800 text-white font-bold rounded-lg shadow-md text-xs transition-all" title="Simpan nilai siswa ini dan langsung buka siswa berikutnya">
                                <span>Simpan & Lanjut</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                            <button type="submit" name="action" value="preview" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow-md text-xs transition-all">
                                Simpan & Lihat Raport
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Kosongkan Nilai Siswa Ini -->
    <x-modal name="confirm-delete-student-grades" focusable>
        <form method="POST" action="{{ route('grades.destroy-student', $student->id) }}" class="p-6">
            @csrf
            @method('DELETE')

            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-11 h-11 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-bold text-gray-900 leading-snug">
                        Kosongkan Nilai {{ $student->name }}
                    </h2>
                    <p class="mt-2 text-xs sm:text-sm text-gray-600 leading-relaxed">
                        Apakah Anda yakin ingin mengosongkan seluruh nilai mata pelajaran untuk siswa <span class="font-bold text-gray-800">{{ $student->name }}</span>?
                    </p>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100">
                <label class="flex items-center gap-3 cursor-pointer select-none bg-slate-50 border border-gray-200 p-3 rounded-lg hover:bg-slate-100 transition-colors">
                    <input type="checkbox" name="reset_attendance" value="1" checked class="w-4 h-4 rounded text-blue-700 border-gray-300 focus:ring-blue-500">
                    <span class="text-xs sm:text-sm text-gray-700 font-medium">
                        Reset juga data absensi (Sakit, Izin, Alpa) dan catatan raport siswa ini
                    </span>
                </label>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-3 border-t border-gray-100">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Batal
                </x-secondary-button>

                <button
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-xs font-bold shadow-xs transition-all focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2"
                >
                    Ya, Kosongkan Nilai Siswa Ini
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

