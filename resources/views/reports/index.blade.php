<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Daftar Siswa & Cetak Raport') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Cetak raport bayangan tengah semester, unduh PDF arsip seluruh siswa, atau unduh format Excel.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- DROPDOWN FILTER SEMESTER / TAHUN AJARAN -->
                <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-1.5 bg-white p-1 rounded-lg border border-gray-300 shadow-sm">
                    <span class="text-xs font-bold text-gray-600 pl-2">Periode:</span>
                    <select name="academic_year_id" onchange="this.form.submit()"
                        class="text-xs font-bold text-gray-800 border-none bg-transparent py-1 pr-8 pl-1 focus:ring-0 cursor-pointer">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ ($selectedAcademicYear?->id == $year->id) ? 'selected' : '' }}>
                                T.A. {{ $year->name }} - Sem. {{ $year->semester }} ({{ $year->semester_name }}){{ $year->is_active ? ' [AKTIF]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <!-- DOWNLOAD PDF SEMUA RAPORT -->
                <a href="{{ route('reports.print-all', array_filter(['action' => 'pdf', 'academic_year_id' => $selectedAcademicYear?->id])) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg text-sm font-bold shadow-md transition-all"
                    title="Simpan dan unduh seluruh raport siswa dalam 1 file PDF lengkap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Download PDF (Semua)</span>
                </a>

                <!-- CETAK SEMUA RAPORT -->
                <a href="{{ route('reports.print-all', array_filter(['academic_year_id' => $selectedAcademicYear?->id])) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-700 hover:bg-indigo-800 text-white rounded-lg text-sm font-bold shadow-md transition-all"
                    title="Buka halaman cetak massal semua siswa">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Semua</span>
                </a>

                <!-- EXCEL LEGER -->
                <a href="{{ route('reports.export-leger', array_filter(['academic_year_id' => $selectedAcademicYear?->id])) }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-bold shadow-md transition-all"
                    title="Unduh rekapitulasi leger nilai satu kelas dalam format Excel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Leger Excel</span>
                </a>

                <!-- KOP SURAT -->
                <a href="{{ route('settings.school') }}" class="inline-flex items-center gap-1 px-3 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-xs font-bold shadow-sm transition-all" title="Ubah Judul Kegiatan Raport & Kop Surat">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Kop Surat</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- INFO PERIODE AKTIF SAAT INI -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-blue-900 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 shrink-0"></span>
                    <span>Menampilkan Raport untuk <strong>T.A. {{ $selectedAcademicYear->name ?? '2026/2027' }} - Semester {{ $selectedAcademicYear->semester ?? 1 }} ({{ $selectedAcademicYear?->semester_name ?? 'Ganjil' }})</strong>. Total Siswa: <strong>{{ $students->total() }}</strong>.</span>
                </div>
                <div class="flex items-center gap-2 self-end sm:self-auto">
                    <a href="{{ route('academic-years.index') }}" class="font-bold text-blue-700 hover:text-blue-900 underline flex items-center gap-1">
                        <span>Kelola Semester</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
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
                                <th class="px-6 py-3.5 text-center">Semester / T.A.</th>
                                <th class="px-6 py-3.5 text-center">Aksi Raport</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-center text-gray-700 text-xs font-medium">
                                    {{ $selectedAcademicYear->name ?? '2026/2027' }} (Sem. {{ $selectedAcademicYear->semester ?? 1 }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('reports.preview', array_filter(['student' => $student->id, 'academic_year_id' => $selectedAcademicYear?->id])) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Preview & Cetak
                                        </a>
                                        <a href="{{ route('reports.excel', array_filter(['student' => $student->id, 'academic_year_id' => $selectedAcademicYear?->id])) }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Excel
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 font-medium">
                                    Belum ada data siswa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $students->appends(['academic_year_id' => $selectedAcademicYear?->id])->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
