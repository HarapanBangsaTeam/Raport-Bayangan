<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportCardController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with(['classroom', 'academicYear'])->paginate(20);

        return view('reports.index', compact('students'));
    }

    public function preview(Student $student)
    {
        $student->load(['classroom', 'grades.subject', 'attendance']);
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();

        return view('reports.preview', compact('student', 'school', 'academicYear', 'subjects'));
    }

    public function printAll()
    {
        $students = Student::with(['classroom', 'grades.subject', 'attendance'])->get();
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = AcademicYear::where('is_active', true)->first();
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();

        return view('reports.print-all', compact('students', 'school', 'academicYear', 'subjects'));
    }

    public function excel(Student $student)
    {
        $student->load(['classroom', 'grades.subject', 'attendance']);
        $school = SchoolSetting::first() ?? new SchoolSetting([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();

        $templatePath = base_path('storage/templates/Raport_Bayangan_Polos.xlsx');
        if (! file_exists($templatePath)) {
            $templatePath = 'C:/laragon/www/SMPHB/Raport bayangan/Raport Bayangan Polos.xlsx';
        }

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($templatePath);

        $wsData = $spreadsheet->getSheetByName('DATA');
        $wsIn = $spreadsheet->getSheetByName('INPUT_NILAI');
        $wsRaport = $spreadsheet->getSheetByName('RAPORT');

        $subjectCount = $subjects->count();
        $baseSlots = 17;

        // If subjects exceed template's base slots (17), insert extra rows dynamically
        if ($subjectCount > $baseSlots) {
            $extra = $subjectCount - $baseSlots;
            if ($wsData) {
                $wsData->insertNewRowBefore(32, $extra);
            }
            if ($wsIn) {
                $wsIn->insertNewRowBefore(26, $extra);
            }
            if ($wsRaport) {
                $wsRaport->insertNewRowBefore(32, $extra);

                for ($k = 1; $k <= $extra; $k++) {
                    $rRaport = $baseSlots + $k + 14;
                    $rIn = $baseSlots + $k + 8;

                    $wsRaport->getRowDimension($rRaport)->setRowHeight(19);
                    $wsRaport->duplicateStyle($wsRaport->getStyle('A31:G31'), "A{$rRaport}:G{$rRaport}");

                    $wsRaport->setCellValue("A{$rRaport}", $baseSlots + $k);
                    $wsRaport->setCellValue("B{$rRaport}", "=IF(OR(INPUT_NILAI!B{$rIn}=\"\",INPUT_NILAI!B{$rIn}=0),\"\",INPUT_NILAI!B{$rIn})");
                    $wsRaport->setCellValue("D{$rRaport}", "=IF(INPUT_NILAI!C{$rIn}=\"\",\"\",INPUT_NILAI!C{$rIn})");
                    $wsRaport->setCellValue("E{$rRaport}", "=IF(INPUT_NILAI!D{$rIn}=\"\",\"\",INPUT_NILAI!D{$rIn})");
                    $wsRaport->setCellValue("F{$rRaport}", "=IF(INPUT_NILAI!E{$rIn}=\"\",\"\",INPUT_NILAI!E{$rIn})");
                    $wsRaport->setCellValue("G{$rRaport}", "=IF(INPUT_NILAI!F{$rIn}=\"\",\"\",INPUT_NILAI!F{$rIn})");
                }

                $avgRow = 15 + $subjectCount;
                $overallAvgRow = $avgRow + 1;
                $lastSubjRow = 14 + $subjectCount;
                $wsRaport->setCellValue("D{$avgRow}", "=IFERROR(ROUND(AVERAGE(D15:D{$lastSubjRow}),0),\"\")");
                $wsRaport->setCellValue("E{$avgRow}", "=IFERROR(ROUND(AVERAGE(E15:E{$lastSubjRow}),0),\"\")");
                $wsRaport->setCellValue("F{$avgRow}", "=IFERROR(ROUND(AVERAGE(F15:F{$lastSubjRow}),0),\"\")");
                $wsRaport->setCellValue("G{$avgRow}", "=IFERROR(ROUND(AVERAGE(G15:G{$lastSubjRow}),0),\"\")");
                $wsRaport->setCellValue("F{$overallAvgRow}", "=IFERROR(ROUND(AVERAGE(D15:G{$lastSubjRow}),0),\"\")");

                $inSakitRow = 28 + $extra;
                $inIzinRow = 29 + $extra;
                $inAlpaRow = 30 + $extra;
                $inNoteRow = 31 + $extra;

                $raportSakitRow = 36 + $extra;
                $raportIzinRow = 37 + $extra;
                $raportAlpaRow = 38 + $extra;

                $wsRaport->setCellValue("B{$raportSakitRow}", "=\"Sakit = \"&IF(INPUT_NILAI!B{$inSakitRow}<>\"\",INPUT_NILAI!B{$inSakitRow},0)");
                $wsRaport->setCellValue("C{$raportSakitRow}", "=IF(INPUT_NILAI!B{$inNoteRow}<>\"\",\"\"&INPUT_NILAI!B{$inNoteRow},\"Tetap semangat belajar ya nak!\")");
                $wsRaport->setCellValue("B{$raportIzinRow}", "=\"Izin = \"&IF(INPUT_NILAI!B{$inIzinRow}<>\"\",INPUT_NILAI!B{$inIzinRow},0)");
                $wsRaport->setCellValue("B{$raportAlpaRow}", "=\"Alpa = \"&IF(INPUT_NILAI!B{$inAlpaRow}<>\"\",INPUT_NILAI!B{$inAlpaRow},0)");
            }
        }

        // Update Sheet DATA
        if ($wsData) {
            $wsData->setCellValue('B3', $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG');
            $wsData->setCellValue('B4', $school->alamat ?? 'Jl. R.E. Martadinata no. 8, RT.02/RW.09, Adiarsa Barat, Karawang');
            $wsData->setCellValue('B5', $school->judul_laporan ?? 'Laporan Hasil Belajar Tengah Semester 1 (Raport Bayangan)');
            $wsData->setCellValue('B6', $academicYear->name ?? '2026/2027');
            $wsData->setCellValue('B7', $school->nama_kepala_sekolah ?? 'Juliana Ruth Mathilda Hutapea, S.Th');
            $wsData->setCellValueExplicit('B8', (string) ($school->nip_kepala_sekolah ?? '02980708020780'), DataType::TYPE_STRING);
            $wsData->setCellValue('B9', $school->nama_wali_kelas ?? 'Farida Donna Gultom, S.Pd');
            $wsData->setCellValue('B10', $school->nip_wali_kelas ?? '');
            $wsData->setCellValue('B11', $school->tanggal_raport ?? 'Karawang, 02 Oktober 2026');

            for ($i = 1; $i <= max($baseSlots, $subjectCount); $i++) {
                $dataRow = 14 + $i;
                if ($i <= $subjectCount) {
                    $wsData->setCellValue("A{$dataRow}", $i);
                    $wsData->setCellValue("B{$dataRow}", $subjects[$i - 1]->name);
                } else {
                    $wsData->setCellValue("A{$dataRow}", '');
                    $wsData->setCellValue("B{$dataRow}", '');
                }
            }
        }

        // Key student grades by subject_id
        $gradesBySubject = $student->grades
            ->when($academicYear, fn ($collection) => $collection->where('academic_year_id', $academicYear->id))
            ->keyBy('subject_id');

        // Update Sheet INPUT_NILAI
        if ($wsIn) {
            $wsIn->setCellValue('B3', $student->name);
            $wsIn->setCellValue('B4', $student->classroom->name ?? 'VII');
            $nisnNis = ($student->nisn ? $student->nisn : '').($student->nis ? ' - '.$student->nis : '');
            $wsIn->setCellValue('B5', $nisnNis);
            $wsIn->setCellValue('B6', $academicYear->name ?? '2026/2027');

            $extra = max(0, $subjectCount - $baseSlots);
            $wsIn->setCellValue('B'.(28 + $extra), $student->attendance->sakit ?? 0);
            $wsIn->setCellValue('B'.(29 + $extra), $student->attendance->izin ?? 0);
            $wsIn->setCellValue('B'.(30 + $extra), $student->attendance->alpa ?? 0);
            $wsIn->setCellValue('B'.(31 + $extra), $student->attendance->note ?? 'Tetap semangat belajar ya nak!');

            for ($i = 1; $i <= max($baseSlots, $subjectCount); $i++) {
                $inRow = 8 + $i;
                if ($i <= $subjectCount) {
                    $subj = $subjects[$i - 1];
                    $wsIn->setCellValue("A{$inRow}", $i);
                    $wsIn->setCellValue("B{$inRow}", '=DATA!B'.(14 + $i));

                    $g = $gradesBySubject->get($subj->id);
                    $wsIn->setCellValue('C'.$inRow, ($g && $g->pr !== null && is_numeric($g->pr)) ? (int) round((float) $g->pr) : '');
                    $wsIn->setCellValue('D'.$inRow, ($g && $g->uh !== null && is_numeric($g->uh)) ? (int) round((float) $g->uh) : '');
                    $wsIn->setCellValue('E'.$inRow, ($g && $g->tertulis !== null && is_numeric($g->tertulis)) ? (int) round((float) $g->tertulis) : '');
                    $wsIn->setCellValue('F'.$inRow, ($g && $g->praktek !== null && is_numeric($g->praktek)) ? (int) round((float) $g->praktek) : '');
                } else {
                    $wsIn->setCellValue("A{$inRow}", '');
                    $wsIn->setCellValue("B{$inRow}", '');
                    $wsIn->setCellValue("C{$inRow}", '');
                    $wsIn->setCellValue("D{$inRow}", '');
                    $wsIn->setCellValue("E{$inRow}", '');
                    $wsIn->setCellValue("F{$inRow}", '');
                }
            }
        }

        // Update Sheet RAPORT (set numbering and hide rows if fewer than base slots)
        if ($wsRaport) {
            for ($i = 1; $i <= max($baseSlots, $subjectCount); $i++) {
                $raportRow = 14 + $i;
                if ($i <= $subjectCount) {
                    $wsRaport->setCellValue("A{$raportRow}", $i);
                    $wsRaport->getRowDimension($raportRow)->setVisible(true);
                } else {
                    $wsRaport->setCellValue("A{$raportRow}", '');
                    $wsRaport->getRowDimension($raportRow)->setVisible(false);
                }
            }
        }

        // Set active sheet to RAPORT
        $spreadsheet->setActiveSheetIndexByName('RAPORT');

        $cleanName = preg_replace('/[^A-Za-z0-9_\- ]/', '', $student->name);
        $nis = $student->nis ?? '000';
        $filename = "Raport Bayangan - {$cleanName} ({$nis}).xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export rekapitulasi leger nilai seluruh siswa dalam 1 file Excel.
     */
    public function exportLeger()
    {
        $school = SchoolSetting::first();
        $academicYear = AcademicYear::where('is_active', true)->first();
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
        $students = Student::with(['classroom', 'grades', 'attendance'])->orderBy('name')->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('LEGER NILAI');

        // Header info
        $sheet->setCellValue('A1', $school->nama_sekolah ?? 'SMP HARAPAN BANGSA KARAWANG');
        $sheet->setCellValue('A2', 'REKAPITULASI LEGER NILAI BELAJAR SISWA (RAPORT BAYANGAN)');
        $sheet->setCellValue('A3', 'Tahun Pelajaran: '.($academicYear->name ?? '2026/2027').' - Semester '.($academicYear->semester ?? 1));

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(10);

        // Columns definition
        $colIdx = 1;
        $sheet->setCellValue([$colIdx++, 5], 'No');
        $sheet->setCellValue([$colIdx++, 5], 'NISN');
        $sheet->setCellValue([$colIdx++, 5], 'NIS');
        $sheet->setCellValue([$colIdx++, 5], 'Nama Siswa');
        $sheet->setCellValue([$colIdx++, 5], 'Kelas');

        foreach ($subjects as $subject) {
            $sheet->setCellValue([$colIdx++, 5], $subject->name);
        }

        $colTotal = $colIdx++;
        $colRata = $colIdx++;
        $colSakit = $colIdx++;
        $colIzin = $colIdx++;
        $colAlpa = $colIdx++;
        $colCatatan = $colIdx++;

        $sheet->setCellValue([$colTotal, 5], 'Total');
        $sheet->setCellValue([$colRata, 5], 'Rata-rata');
        $sheet->setCellValue([$colSakit, 5], 'Sakit');
        $sheet->setCellValue([$colIzin, 5], 'Izin');
        $sheet->setCellValue([$colAlpa, 5], 'Alpa');
        $sheet->setCellValue([$colCatatan, 5], 'Catatan Wali Kelas');

        $lastColLetter = Coordinate::stringFromColumnIndex($colCatatan - 1);

        // Merge and center title rows across table width
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->mergeCells("A3:{$lastColLetter}3");
        $sheet->getStyle("A1:{$lastColLetter}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Style header row
        $headerRange = "A5:{$lastColLetter}5";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(28);

        // Fill data rows
        $row = 6;
        foreach ($students as $index => $student) {
            $col = 1;
            $sheet->setCellValue([$col++, $row], $index + 1);
            $sheet->setCellValueExplicit([$col++, $row], (string) ($student->nisn ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$col++, $row], (string) ($student->nis ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValue([$col++, $row], $student->name);
            $sheet->setCellValue([$col++, $row], $student->classroom->name ?? '-');

            $gradesBySubject = $student->grades->keyBy('subject_id');
            $studentTotal = 0;
            $subjectCountScored = 0;

            foreach ($subjects as $subject) {
                $g = $gradesBySubject->get($subject->id);
                $scores = [];
                if ($g) {
                    if ($g->pr !== null && is_numeric($g->pr)) {
                        $scores[] = (float) $g->pr;
                    }
                    if ($g->uh !== null && is_numeric($g->uh)) {
                        $scores[] = (float) $g->uh;
                    }
                    if ($g->tertulis !== null && is_numeric($g->tertulis)) {
                        $scores[] = (float) $g->tertulis;
                    }
                    if ($g->praktek !== null && is_numeric($g->praktek)) {
                        $scores[] = (float) $g->praktek;
                    }
                }

                if (count($scores) > 0) {
                    $avg = (int) round(array_sum($scores) / count($scores));
                    $sheet->setCellValue([$col++, $row], $avg);
                    $studentTotal += $avg;
                    $subjectCountScored++;
                } else {
                    $sheet->setCellValue([$col++, $row], '-');
                }
            }

            $overallAvg = $subjectCountScored > 0 ? round($studentTotal / $subjectCountScored, 1) : '-';

            $sheet->setCellValue([$colTotal, $row], $studentTotal > 0 ? $studentTotal : '-');
            $sheet->setCellValue([$colRata, $row], $overallAvg);
            $sheet->setCellValue([$colSakit, $row], $student->attendance->sakit ?? 0);
            $sheet->setCellValue([$colIzin, $row], $student->attendance->izin ?? 0);
            $sheet->setCellValue([$colAlpa, $row], $student->attendance->alpa ?? 0);
            $sheet->setCellValue([$colCatatan, $row], $student->attendance->note ?? '-');

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        $lastRow = max(6, $row - 1);
        $dataRange = "A6:{$lastColLetter}{$lastRow}";
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A6:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E6:'.Coordinate::stringFromColumnIndex($colAlpa)."{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto size columns
        for ($c = 1; $c < $colCatatan; $c++) {
            $colLetter = Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colCatatan - 1))->setWidth(35);

        $cleanYear = preg_replace('/[^A-Za-z0-9_\-]/', '_', $academicYear->name ?? '2026_2027');
        $filename = "Leger_Nilai_Kelas_{$cleanYear}_Semester_".($academicYear->semester ?? 1).'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
