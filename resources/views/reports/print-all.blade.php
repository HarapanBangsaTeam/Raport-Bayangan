<x-app-layout>
    <div x-data="{
        paperSize: 'a4',
        spacing: 'normal',
        isPdfMode: {{ request('action') === 'pdf' ? 'true' : 'false' }},
        init() {
            if (this.isPdfMode) {
                setTimeout(() => {
                    window.print();
                }, 600);
            }
        },
        triggerPdf() {
            window.print();
        },
        get pageStyle() {
            if (this.paperSize === 'f4') {
                return '@page { size: 215mm 330mm portrait; margin: 0 !important; } .print-container, .page-break { width: 215mm !important; height: 330mm !important; min-height: 330mm !important; max-height: 330mm !important; }';
            } else if (this.paperSize === 'legal') {
                return '@page { size: 216mm 356mm portrait; margin: 0 !important; } .print-container, .page-break { width: 216mm !important; height: 356mm !important; min-height: 356mm !important; max-height: 356mm !important; }';
            }
            return '@page { size: 210mm 297mm portrait; margin: 0 !important; } .print-container, .page-break { width: 210mm !important; height: 297mm !important; min-height: 297mm !important; max-height: 297mm !important; }';
        }
    }">
        <x-slot name="header">
            <!-- TOOLBAR KONTROL CETAK MASSAL -->
            <div class="print:hidden flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                <div>
                    <h2 class="font-extrabold text-xl text-gray-900 leading-tight">
                        {{ __('Cetak & Backup PDF Raport (Semua Siswa)') }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        T.A. {{ $academicYear->name ?? '2026/2027' }} - Semester {{ $academicYear->semester ?? 1 }} ({{ $academicYear?->semester_name ?? 'Ganjil' }}). Total {{ $students->count() }} siswa.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- OPSI UKURAN KERTAS -->
                    <div class="flex items-center gap-1.5 bg-gray-100 p-1 rounded-lg border border-gray-200">
                        <span class="text-xs font-bold text-gray-600 px-2">Kertas:</span>
                        <button type="button" @click="paperSize = 'a4'"
                            :class="paperSize === 'a4' ? 'bg-blue-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            A4
                        </button>
                        <button type="button" @click="paperSize = 'f4'"
                            :class="paperSize === 'f4' ? 'bg-blue-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            F4 / Folio
                        </button>
                        <button type="button" @click="paperSize = 'legal'"
                            :class="paperSize === 'legal' ? 'bg-blue-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            Legal
                        </button>
                    </div>

                    <!-- OPSI KERAPATAN / SPACING -->
                    <div class="flex items-center gap-1.5 bg-gray-100 p-1 rounded-lg border border-gray-200">
                        <span class="text-xs font-bold text-gray-600 px-2">Format:</span>
                        <button type="button" @click="spacing = 'compact'"
                            :class="spacing === 'compact' ? 'bg-slate-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            Kompak
                        </button>
                        <button type="button" @click="spacing = 'normal'"
                            :class="spacing === 'normal' ? 'bg-slate-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            Proporsional
                        </button>
                        <button type="button" @click="spacing = 'spacious'"
                            :class="spacing === 'spacious' ? 'bg-slate-700 text-white shadow-sm' : 'text-gray-700 hover:bg-gray-200'"
                            class="px-2.5 py-1 text-xs font-bold rounded-md transition-all">
                            Lega
                        </button>
                    </div>

                    <!-- TOMBOL AKSI UTAMA -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('settings.school') }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold py-2 px-3 rounded-lg shadow-sm text-xs flex items-center gap-1 transition-all" title="Edit Kop Surat & Nama Kegiatan">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Kop Surat
                        </a>
                        <a href="{{ route('reports.index', ['academic_year_id' => $academicYear?->id]) }}" class="bg-slate-700 hover:bg-slate-800 text-white font-bold py-2 px-3.5 rounded-lg shadow-sm text-xs transition-all">
                            Kembali
                        </a>
                        <button type="button" @click="triggerPdf()" class="bg-rose-700 hover:bg-rose-800 text-white font-bold py-2 px-3.5 rounded-lg shadow-md text-xs flex items-center gap-1.5 transition-all" title="Simpan sebagai 1 file PDF lengkap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Simpan / Download PDF
                        </button>
                        <button onclick="window.print()" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-md text-xs flex items-center gap-1.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak Printer
                        </button>
                    </div>
                </div>
            </div>
        </x-slot>

        <!-- DYNAMIC STYLE BERDASARKAN UKURAN KERTAS & SPACING -->
        <style x-text="pageStyle"></style>
        <style>
            @media print {
                nav, header, .print-hidden, .no-print {
                    display: none !important;
                }
                body {
                    background: white !important;
                    color: black !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
                .py-6 {
                    padding: 0 !important;
                    margin: 0 !important;
                }
                .max-w-4xl {
                    max-width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
                .page-break {
                    position: relative !important;
                    width: 210mm !important;
                    height: 297mm !important;
                    min-height: 297mm !important;
                    max-height: 297mm !important;
                    box-shadow: none !important;
                    border: none !important;
                    overflow: hidden !important;
                    box-sizing: border-box !important;
                    padding: 8mm 10mm !important;
                    margin: 0 auto !important;
                    page-break-after: always !important;
                    break-after: page !important;
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }
                .raport-table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                    table-layout: fixed !important;
                    border: 1.5px solid #000000 !important;
                    box-sizing: border-box !important;
                }
                .raport-table th, .raport-table td {
                    border: 1px solid #000000 !important;
                    box-sizing: border-box !important;
                }
                .raport-table th:first-child, .raport-table td:first-child {
                    border-left: 1.5px solid #000000 !important;
                }
                .raport-table th:last-child, .raport-table td:last-child {
                    border-right: 1.5px solid #000000 !important;
                }
                .identity-box {
                    border: 1.5px solid #000000 !important;
                    box-sizing: border-box !important;
                }
                .footer-box {
                    border: 1.5px solid #000000 !important;
                    box-sizing: border-box !important;
                }
                .watermark-container {
                    position: absolute !important;
                    inset: 0 !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    width: 100% !important;
                    height: 100% !important;
                    overflow: hidden !important;
                    pointer-events: none !important;
                    z-index: 0 !important;
                }
                .watermark-container img {
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: fill !important;
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }
        </style>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-12 print:space-y-0">
                <!-- PETUNJUK SIMPAN PDF (HANYA DITAMPILKAN DI LAYAR BROWSER, BUKAN SAAT CETAK) -->
                <div class="print:hidden bg-amber-50 border border-amber-300 rounded-xl p-4 shadow-sm text-amber-900">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-amber-100 rounded-lg text-amber-700 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="space-y-1 text-xs leading-relaxed">
                            <h4 class="font-bold text-sm text-amber-950">Panduan Mengunduh Raport Format PDF (Backup Seluruh Siswa)</h4>
                            <p>
                                1. Klik tombol <strong>"Simpan / Download PDF"</strong> atau <strong>"Cetak Printer"</strong> di atas.
                            </p>
                            <p>
                                2. Pada dialog cetak browser (Chrome / Edge), pilih <strong>Destination (Tujuan)</strong>: <strong>"Save as PDF" (Simpan sebagai PDF)</strong>.
                            </p>
                            <p>
                                3. Centang opsi <strong>"Background graphics" (Grafik latar belakang)</strong> agar watermark resmi SMP Harapan Bangsa tercetak jernih.
                            </p>
                            <p>
                                4. Klik <strong>Save</strong>. Satu berkas PDF utuh berisi raport seluruh siswa (1 halaman per siswa) akan langsung tersimpan.
                            </p>
                        </div>
                    </div>
                </div>

                @foreach($students as $student)
                <!-- Lembar Raport Kertas per Siswa -->
                <div class="page-break relative bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:p-8 font-sans text-black leading-normal box-border w-full">
                    
                    <!-- Background Watermark Logo & Wavy Text Kertas Raport -->
                    <div class="watermark-container absolute inset-0 pointer-events-none z-0 overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/watermark_wavy.png') }}?v={{ filemtime(public_path('images/watermark_wavy.png')) }}" alt="Watermark SMP Harapan Bangsa" class="w-full h-full object-cover">
                    </div>

                    <div class="relative z-10 w-full">
                        <!-- KOP SURAT (LOGO DI AREA JUDUL KIRI) -->
                        <div class="flex items-center gap-4"
                            :class="{
                                'mb-2 pb-1': spacing === 'compact',
                                'mb-3 pb-2': spacing === 'normal',
                                'mb-4 pb-3': spacing === 'spacious'
                            }">
                            <div class="w-20 shrink-0 flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}?v={{ filemtime(public_path('images/logo.png')) }}" alt="Logo SMP Harapan Bangsa"
                                    :class="{
                                        'h-16': spacing === 'compact',
                                        'h-20': spacing === 'normal',
                                        'h-24': spacing === 'spacious'
                                    }"
                                    class="w-auto object-contain transition-all">
                            </div>
                            <div class="flex-grow text-center pr-10">
                                <h1 class="font-extrabold uppercase tracking-wider text-black text-lg sm:text-2xl leading-tight">
                                    {{ $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG' }}
                                </h1>
                                <p class="text-xs text-gray-800 font-medium mt-0.5">
                                    {{ $school->alamat ?? 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang' }}
                                </p>
                                <h2 class="text-sm font-bold text-black mt-1">
                                    {{ $school->judul_laporan ?? 'Laporan Hasil Belajar Tengah Semester ' . ($academicYear->semester ?? 1) . ' (Raport Bayangan)' }}
                                </h2>
                                <h3 class="text-sm font-bold text-black">
                                    Tahun Pelajaran {{ $academicYear->name ?? '2026/2027' }}
                                </h3>
                            </div>
                        </div>

                        <!-- IDENTITAS SISWA (BORDER BOX) -->
                        <div class="identity-box border border-black text-xs font-bold w-full"
                            :class="{
                                'mb-2': spacing === 'compact',
                                'mb-3': spacing === 'normal',
                                'mb-4': spacing === 'spacious'
                            }">
                            <div class="grid grid-cols-12 border-b border-black">
                                <div class="col-span-3 border-r border-black uppercase"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">NAMA SISWA</div>
                                <div class="col-span-9 uppercase font-medium"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">{{ $student->name }}</div>
                            </div>
                            <div class="grid grid-cols-12 border-b border-black">
                                <div class="col-span-3 border-r border-black uppercase"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">KELAS</div>
                                <div class="col-span-9 font-medium"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">{{ $student->classroom->name ?? 'VII' }}</div>
                            </div>
                            <div class="grid grid-cols-12">
                                <div class="col-span-3 border-r border-black uppercase"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">NISN - NIS</div>
                                <div class="col-span-9 font-medium font-mono"
                                    :class="spacing === 'compact' ? 'py-0.5 px-2' : 'py-1 px-3'">
                                    {{ ($student->nisn ? $student->nisn : '') . ($student->nis ? ' - ' . $student->nis : '') }}
                                </div>
                            </div>
                        </div>

                        <!-- JUDUL SECTION -->
                        <div class="font-bold text-xs uppercase mb-1.5 tracking-wide">
                            CAPAIAN KOMPETENSI
                        </div>

                        <!-- TABEL NILAI (GARIS PINGGIR 100% UTUH) -->
                        <table class="raport-table w-full border-collapse border border-black mb-1 text-xs table-fixed">
                            <thead>
                                <tr class="bg-transparent font-bold">
                                    <th rowspan="2" class="border border-black text-center" style="width: 6%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">No</th>
                                    <th rowspan="2" class="border border-black text-center" style="width: 44%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">MATA PELAJARAN</th>
                                    <th colspan="2" class="border border-black text-center" style="width: 25%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">FORMATIF HARIAN</th>
                                    <th colspan="2" class="border border-black text-center" style="width: 25%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">SUMATIF</th>
                                </tr>
                                <tr class="bg-transparent font-bold">
                                    <th class="border border-black text-center" style="width: 12.5%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">PR</th>
                                    <th class="border border-black text-center" style="width: 12.5%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">UH</th>
                                    <th class="border border-black text-center" style="width: 12.5%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">TERTULIS</th>
                                    <th class="border border-black text-center" style="width: 12.5%;"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">PRAKTEK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $activeSubjects = $subjects ?? \App\Models\Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
                                    $gradesBySubject = $student->grades->keyBy('subject_id');
                                    
                                    $allPr = [];
                                    $allUh = [];
                                    $allTertulis = [];
                                    $allPraktek = [];
                                    $allScores = [];

                                    foreach ($activeSubjects as $subj) {
                                        $g = $gradesBySubject->get($subj->id);
                                        if ($g) {
                                            if ($g->pr !== null && is_numeric($g->pr)) {
                                                $val = (int)round((float)$g->pr);
                                                $allPr[] = $val;
                                                $allScores[] = $val;
                                            }
                                            if ($g->uh !== null && is_numeric($g->uh)) {
                                                $val = (int)round((float)$g->uh);
                                                $allUh[] = $val;
                                                $allScores[] = $val;
                                            }
                                            if ($g->tertulis !== null && is_numeric($g->tertulis)) {
                                                $val = (int)round((float)$g->tertulis);
                                                $allTertulis[] = $val;
                                                $allScores[] = $val;
                                            }
                                            if ($g->praktek !== null && is_numeric($g->praktek)) {
                                                $val = (int)round((float)$g->praktek);
                                                $allPraktek[] = $val;
                                                $allScores[] = $val;
                                            }
                                        }
                                    }

                                    $avgPr = count($allPr) > 0 ? (int)round(array_sum($allPr) / count($allPr)) : null;
                                    $avgUh = count($allUh) > 0 ? (int)round(array_sum($allUh) / count($allUh)) : null;
                                    $avgTertulis = count($allTertulis) > 0 ? (int)round(array_sum($allTertulis) / count($allTertulis)) : null;
                                    $avgPraktek = count($allPraktek) > 0 ? (int)round(array_sum($allPraktek) / count($allPraktek)) : null;
                                    $avgTotal = count($allScores) > 0 ? (int)round(array_sum($allScores) / count($allScores)) : null;
                                @endphp

                                @foreach($activeSubjects as $index => $subject)
                                @php
                                    $grade = $gradesBySubject->get($subject->id);
                                    $prVal = ($grade && $grade->pr !== null && is_numeric($grade->pr)) ? (int)round((float)$grade->pr) : null;
                                    $uhVal = ($grade && $grade->uh !== null && is_numeric($grade->uh)) ? (int)round((float)$grade->uh) : null;
                                    $trtVal = ($grade && $grade->tertulis !== null && is_numeric($grade->tertulis)) ? (int)round((float)$grade->tertulis) : null;
                                    $prkVal = ($grade && $grade->praktek !== null && is_numeric($grade->praktek)) ? (int)round((float)$grade->praktek) : null;
                                @endphp
                                <tr>
                                    <td class="border border-black text-center font-normal"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $index + 1 }}</td>
                                    <td class="border border-black font-normal"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $subject->name }}</td>
                                    <td class="border border-black text-center {{ $prVal !== null && $prVal < 70 ? 'text-red-600 font-bold' : '' }}"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">
                                        {{ $prVal !== null ? $prVal : '-' }}
                                    </td>
                                    <td class="border border-black text-center {{ $uhVal !== null && $uhVal < 70 ? 'text-red-600 font-bold' : '' }}"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">
                                        {{ $uhVal !== null ? $uhVal : '-' }}
                                    </td>
                                    <td class="border border-black text-center {{ $trtVal !== null && $trtVal < 70 ? 'text-red-600 font-bold' : '' }}"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">
                                        {{ $trtVal !== null ? $trtVal : '-' }}
                                    </td>
                                    <td class="border border-black text-center {{ $prkVal !== null && $prkVal < 70 ? 'text-red-600 font-bold' : '' }}"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">
                                        {{ $prkVal !== null ? $prkVal : '-' }}
                                    </td>
                                </tr>
                                @endforeach

                                <!-- BARIS RATA-RATA -->
                                <tr class="font-bold">
                                    <td colspan="2" class="border border-black text-center uppercase"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">RATA-RATA</td>
                                    <td class="border border-black text-center"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $avgPr !== null ? $avgPr : '' }}</td>
                                    <td class="border border-black text-center"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $avgUh !== null ? $avgUh : '' }}</td>
                                    <td class="border border-black text-center"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $avgTertulis !== null ? $avgTertulis : '' }}</td>
                                    <td class="border border-black text-center"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">{{ $avgPraktek !== null ? $avgPraktek : '' }}</td>
                                </tr>

                                <!-- BARIS RATA-RATA KESELURUHAN -->
                                <tr class="font-bold">
                                    <td colspan="4" class="border border-black text-center uppercase"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">RATA-RATA KESELURUHAN</td>
                                    <td colspan="2" class="border border-black text-center font-extrabold text-sm"
                                        :class="spacing === 'compact' ? 'py-0.5 px-1' : (spacing === 'spacious' ? 'py-1.5 px-2' : 'py-1 px-1.5')">
                                        {{ $avgTotal !== null ? $avgTotal : '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- CATATAN OPSIONAL -->
                        <div class="text-[10px] italic text-gray-700"
                            :class="{
                                'mb-1.5': spacing === 'compact',
                                'mb-2.5': spacing === 'normal',
                                'mb-4': spacing === 'spacious'
                            }">
                            *) Mata Pelajaran Opsional / Tambahan
                        </div>

                        <!-- KETERANGAN, CATATAN, TANDA TANGAN ORANG TUA -->
                        <div class="footer-box border border-black text-xs"
                            :class="{
                                'mb-2': spacing === 'compact',
                                'mb-3.5': spacing === 'normal',
                                'mb-5': spacing === 'spacious'
                            }">
                            <div class="grid grid-cols-12 border-b border-black font-bold text-center">
                                <div class="col-span-4 p-1 border-r border-black">Keterangan</div>
                                <div class="col-span-4 p-1 border-r border-black">Catatan</div>
                                <div class="col-span-4 p-1">Tanda Tangan Orang Tua</div>
                            </div>
                            <div class="grid grid-cols-12"
                                :class="{
                                    'min-h-[50px]': spacing === 'compact',
                                    'min-h-[64px]': spacing === 'normal',
                                    'min-h-[80px]': spacing === 'spacious'
                                }">
                                <!-- Kolom Absensi -->
                                <div class="col-span-4 border-r border-black text-xs font-semibold">
                                    <div class="grid grid-cols-12 border-b border-black p-1">
                                        <div class="col-span-3 text-center border-r border-black font-bold">S</div>
                                        <div class="col-span-9 pl-2">Sakit = {{ $student->attendance->sakit ?? 0 }}</div>
                                    </div>
                                    <div class="grid grid-cols-12 border-b border-black p-1">
                                        <div class="col-span-3 text-center border-r border-black font-bold">I</div>
                                        <div class="col-span-9 pl-2">Izin = {{ $student->attendance->izin ?? 0 }}</div>
                                    </div>
                                    <div class="grid grid-cols-12 p-1">
                                        <div class="col-span-3 text-center border-r border-black font-bold">A</div>
                                        <div class="col-span-9 pl-2">Alpa = {{ $student->attendance->alpa ?? 0 }}</div>
                                    </div>
                                </div>
                                <!-- Kolom Catatan -->
                                <div class="col-span-4 border-r border-black p-2 flex items-center justify-center text-center font-medium">
                                    {{ $student->attendance->note ?? 'Tetap semangat belajar ya nak!' }}
                                </div>
                                <!-- Kolom Tanda Tangan Orang Tua -->
                                <div class="col-span-4 p-2 flex flex-col justify-end items-center">
                                    <div class="w-4/5 border-b border-dotted border-black mb-1"></div>
                                </div>
                            </div>
                        </div>

                        <!-- TANDA TANGAN KEPALA SEKOLAH DAN WALI KELAS -->
                        <div class="grid grid-cols-2 text-xs pt-1">
                            <div class="text-center">
                                <p class="font-normal">Mengetahui,</p>
                                <p class="font-normal"
                                    :class="{
                                        'mb-10': spacing === 'compact',
                                        'mb-14': spacing === 'normal',
                                        'mb-20': spacing === 'spacious'
                                    }">Kepala Sekolah</p>
                                <p class="font-bold underline text-black">{{ $school?->nama_kepala_sekolah ?? 'Juliana Ruth Mathilda Hutapea, S.Th' }}</p>
                                <p class="text-xs">{{ $school?->nip_kepala_sekolah ? 'NIPY: ' . $school->nip_kepala_sekolah : '' }}</p>
                            </div>
                            <div class="text-center">
                                <p class="font-normal">{{ $school?->tanggal_raport ?? 'Karawang, 02 Oktober 2026' }}</p>
                                <p class="font-normal"
                                    :class="{
                                        'mb-10': spacing === 'compact',
                                        'mb-14': spacing === 'normal',
                                        'mb-20': spacing === 'spacious'
                                    }">Wali Kelas</p>
                                <p class="font-bold underline text-black">{{ $school?->nama_wali_kelas ?? 'Farida Donna Gultom, S.Pd' }}</p>
                                <p class="text-xs">{{ $school?->nip_wali_kelas ? 'NIPY: ' . $school->nip_wali_kelas : 'NIPY: -' }}</p>
                            </div>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
