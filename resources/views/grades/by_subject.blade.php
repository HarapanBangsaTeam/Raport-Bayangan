<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Input Nilai Cepat per Mata Pelajaran') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Isi nilai seluruh siswa sekaligus untuk satu mata pelajaran tertentu.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-100 text-blue-900 border border-blue-300">
                    Total Siswa: {{ $students->count() }}
                </span>
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

            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-600 p-4 rounded-xl shadow-xs">
                    <ul class="text-xs text-rose-800 font-semibold list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tab Navigasi Mode Input Nilai -->
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
                <a href="{{ route('grades.index', array_filter(['academic_year_id' => $academicYear?->id])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-white text-gray-700 hover:bg-gray-100 border border-gray-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Mode per Siswa</span>
                </a>
                <a href="{{ route('grades.by-subject', array_filter(['academic_year_id' => $academicYear?->id])) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition-all bg-blue-700 text-white shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Mode per Mata Pelajaran (Cepat)</span>
                </a>
            </div>

            <!-- Panel Pilihan Mata Pelajaran -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <label for="subject_picker" class="text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                        Pilih Mata Pelajaran:
                    </label>
                    <select id="subject_picker" 
                            onchange="window.location.href = '{{ route('grades.by-subject') }}?subject_id=' + this.value + '{{ $academicYear ? '&academic_year_id=' . $academicYear->id : '' }}'" 
                            class="rounded-lg border-gray-300 text-sm font-bold text-gray-800 shadow-xs focus:ring-blue-500 focus:border-blue-500 py-2 pl-3 pr-8">
                        @foreach($subjects as $subj)
                            <option value="{{ $subj->id }}" {{ ($selectedSubject?->id === $subj->id) ? 'selected' : '' }}>
                                {{ $subj->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="text-xs text-gray-500 bg-slate-50 border border-gray-200 rounded-lg px-3 py-2 w-full md:w-auto">
                    <span class="font-bold text-gray-700">Tips Pengisian Cepat:</span> Gunakan tombol <kbd class="px-1.5 py-0.5 text-[11px] font-mono bg-white border border-gray-300 rounded shadow-2xs">Tab</kbd> untuk berpindah antar kolom nilai secara langsung.
                </div>
            </div>

            @if($selectedSubject)
            <form action="{{ route('grades.store-by-subject') }}" method="POST">
                @csrf
                <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}">
                <input type="hidden" name="academic_year_id" value="{{ $academicYear?->id }}">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="px-6 py-4 bg-slate-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <div>
                            <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Mata Pelajaran Aktif:</span>
                            <h3 class="text-lg font-extrabold text-gray-900">{{ $selectedSubject->name }}</h3>
                        </div>
                        <div class="text-xs text-gray-500">
                            Tahun Ajaran: <span class="font-bold text-gray-700">{{ $academicYear->name ?? '2026/2027' }} (Semester {{ $academicYear->semester ?? 1 }})</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 text-xs font-bold uppercase tracking-wider">
                                    <th rowspan="2" class="px-4 py-3 text-center w-12 border-r border-gray-200">No</th>
                                    <th rowspan="2" class="px-4 py-3 text-left border-r border-gray-200 min-w-[200px]">Nama Siswa</th>
                                    <th rowspan="2" class="px-4 py-3 text-left border-r border-gray-200 min-w-[120px]">NISN / NIS</th>
                                    <th colspan="2" class="px-4 py-2 text-center border-r border-gray-200 bg-blue-50/60 text-blue-900">Formatif Harian</th>
                                    <th colspan="2" class="px-4 py-2 text-center bg-indigo-50/60 text-indigo-900">Sumatif</th>
                                </tr>
                                <tr class="bg-gray-50 text-gray-700 text-xs font-bold uppercase tracking-wider border-t border-gray-200">
                                    <th class="px-3 py-2 text-center w-28 border-r border-gray-200 bg-blue-50/30">PR</th>
                                    <th class="px-3 py-2 text-center w-28 border-r border-gray-200 bg-blue-50/30">UH</th>
                                    <th class="px-3 py-2 text-center w-28 border-r border-gray-200 bg-indigo-50/30">Tertulis</th>
                                    <th class="px-3 py-2 text-center w-28 bg-indigo-50/30">Praktek</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm">
                                @forelse($students as $index => $student)
                                    @php
                                        $g = $grades->get($student->id);
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-gray-600 font-semibold border-r border-gray-100">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-900 border-r border-gray-100">
                                            {{ $student->name }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700 font-mono text-xs font-medium border-r border-gray-100">
                                            {{ $student->nisn ? $student->nisn : '-' }} / {{ $student->nis ?? '-' }}
                                        </td>
                                        <td class="px-2 py-2 border-r border-gray-100">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="grades[{{ $student->id }}][pr]" 
                                                   value="{{ old('grades.'.$student->id.'.pr', $g->pr ?? '') }}" 
                                                   placeholder="-" 
                                                   class="w-full text-center text-sm font-medium rounded-md border-gray-300 shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-1.5">
                                        </td>
                                        <td class="px-2 py-2 border-r border-gray-100">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="grades[{{ $student->id }}][uh]" 
                                                   value="{{ old('grades.'.$student->id.'.uh', $g->uh ?? '') }}" 
                                                   placeholder="-" 
                                                   class="w-full text-center text-sm font-medium rounded-md border-gray-300 shadow-2xs focus:border-blue-500 focus:ring-blue-500 py-1.5">
                                        </td>
                                        <td class="px-2 py-2 border-r border-gray-100">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="grades[{{ $student->id }}][tertulis]" 
                                                   value="{{ old('grades.'.$student->id.'.tertulis', $g->tertulis ?? '') }}" 
                                                   placeholder="-" 
                                                   class="w-full text-center text-sm font-medium rounded-md border-gray-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                        </td>
                                        <td class="px-2 py-2">
                                            <input type="number" 
                                                   step="0.01" 
                                                   min="0" 
                                                   max="100" 
                                                   name="grades[{{ $student->id }}][praktek]" 
                                                   value="{{ old('grades.'.$student->id.'.praktek', $g->praktek ?? '') }}" 
                                                   placeholder="-" 
                                                   class="w-full text-center text-sm font-medium rounded-md border-gray-300 shadow-2xs focus:border-indigo-500 focus:ring-indigo-500 py-1.5">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada data siswa.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <a href="{{ route('grades.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                            Kembali ke Daftar Siswa
                        </a>
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold transition-all shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Simpan Nilai Mata Pelajaran Ini
                        </button>
                    </div>
                </div>
            </form>
            @endif

        </div>
    </div>
</x-app-layout>
