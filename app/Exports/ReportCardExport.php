<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\SchoolSetting;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportCardExport implements Export, WithEvents, WithTitle
{
    public function __construct(
        public Student $student,
        public ?SchoolSetting $school,
        public ?AcademicYear $academicYear
    ) {}

    public function title(): string
    {
        return 'RAPORT';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildReport($sheet);
            },
        ];
    }

    /**
     * Build the entire report card spreadsheet cell by cell.
     */
    private function buildReport(Worksheet $sheet): void
    {
        $school = $this->school;
        $student = $this->student;
        $academicYear = $this->academicYear;
        $grades = $student->grades->sortBy(fn ($g) => $g->subject->order_number ?? 0)->values();
        $attendance = $student->attendance;

        // =========================================================
        // COLUMN WIDTHS (A-F)
        // =========================================================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(32);
        $sheet->getColumnDimension('C')->setWidth(13);
        $sheet->getColumnDimension('D')->setWidth(13);
        $sheet->getColumnDimension('E')->setWidth(14);
        $sheet->getColumnDimension('F')->setWidth(14);

        // Default font
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $row = 1;

        // =========================================================
        // HEADER SECTION (centered, merged A:F)
        // =========================================================
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", '[ LOGO SEKOLAH ]');
        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", $school->nama_sekolah ?? 'SMP HARAPAN BANGSA');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        $alamat = $school->alamat ?? '';
        if ($school->kelurahan) {
            $alamat .= ', '.$school->kelurahan;
        }
        if ($school->kabupaten) {
            $alamat .= '/'.$school->kabupaten;
        }
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", $alamat);
        $sheet->getStyle("A{$row}")->getFont()->setSize(9);
        $sheet->getRowDimension($row)->setRowHeight(16);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'Laporan Hasil Belajar Tengah Semester '.($academicYear->semester ?? 1));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'Tahun Pelajaran '.($academicYear->name ?? ''));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Style header rows: center all
        $headerLastRow = $row - 1;
        $sheet->getStyle("A1:F{$headerLastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Empty row
        $sheet->getRowDimension($row)->setRowHeight(10);
        $row++;

        // =========================================================
        // IDENTITAS SISWA (bordered box, left-aligned)
        // =========================================================
        $identityStart = $row;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'NAMA SISWA');
        $sheet->setCellValue("C{$row}", $student->name);
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle("C{$row}:F{$row}")->getFont()->setBold(false)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'KELAS');
        $sheet->setCellValue("C{$row}", $student->classroom->name ?? '-');
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'NOMOR INDUK');
        $sheet->setCellValue("C{$row}", $student->nis ?? '-');
        $sheet->getStyle("A{$row}:B{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $identityEnd = $row - 1;

        // Border around identity box
        $sheet->getStyle("A{$identityStart}:F{$identityEnd}")->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("A{$identityStart}:B{$identityEnd}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // Horizontal borders between identity rows
        for ($r = $identityStart; $r <= $identityEnd; $r++) {
            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        }

        // Vertical alignment for identity
        $sheet->getStyle("A{$identityStart}:F{$identityEnd}")->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Empty row
        $sheet->getRowDimension($row)->setRowHeight(10);
        $row++;

        // =========================================================
        // "CAPAIAN KOMPETENSI" LABEL
        // =========================================================
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->setCellValue("A{$row}", 'CAPAIAN KOMPETENSI');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Empty row
        $sheet->getRowDimension($row)->setRowHeight(6);
        $row++;

        // =========================================================
        // TABLE HEADER (2 rows)
        // =========================================================
        $tableHeaderStart = $row;

        // Row 1: merged headers
        $sheet->mergeCells("A{$row}:A".($row + 1)); // No
        $sheet->setCellValue("A{$row}", 'No');

        $sheet->mergeCells("B{$row}:B".($row + 1)); // MATA PELAJARAN
        $sheet->setCellValue("B{$row}", 'MATA PELAJARAN');

        $sheet->mergeCells("C{$row}:D{$row}"); // FORMATIF HARIAN
        $sheet->setCellValue("C{$row}", 'FORMATIF HARIAN');

        $sheet->mergeCells("E{$row}:F{$row}"); // SUMATIF
        $sheet->setCellValue("E{$row}", 'SUMATIF');

        $sheet->getRowDimension($row)->setRowHeight(20);
        $row++;

        // Row 2: sub headers
        $sheet->setCellValue("C{$row}", 'PR');
        $sheet->setCellValue("D{$row}", 'UH');
        $sheet->setCellValue("E{$row}", 'TERTULIS');
        $sheet->setCellValue("F{$row}", 'PRAKTEK');
        $sheet->getRowDimension($row)->setRowHeight(20);

        $tableHeaderEnd = $row;

        // Style table header: bold, center, border
        $sheet->getStyle("A{$tableHeaderStart}:F{$tableHeaderEnd}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $row++;

        // =========================================================
        // GRADE DATA ROWS
        // =========================================================
        $tableDataStart = $row;

        foreach ($grades as $index => $grade) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $grade->subject->name ?? '');
            $sheet->setCellValue("C{$row}", $grade->pr !== null ? (float) $grade->pr : '-');
            $sheet->setCellValue("D{$row}", $grade->uh !== null ? (float) $grade->uh : '-');
            $sheet->setCellValue("E{$row}", $grade->tertulis !== null ? (float) $grade->tertulis : '-');
            $sheet->setCellValue("F{$row}", $grade->praktek !== null ? (float) $grade->praktek : '-');
            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        $tableDataEnd = $row - 1;

        // Style data rows
        $sheet->getStyle("A{$tableDataStart}:F{$tableDataEnd}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);

        // Center the No + value columns
        $sheet->getStyle("A{$tableDataStart}:A{$tableDataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$tableDataStart}:F{$tableDataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Mata pelajaran left-aligned
        $sheet->getStyle("B{$tableDataStart}:B{$tableDataEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        // Font size for data
        $sheet->getStyle("A{$tableDataStart}:F{$tableDataEnd}")->getFont()->setSize(9);

        // =========================================================
        // JUMLAH NILAI (SUM) & RATA-RATA (AVERAGE)
        // =========================================================
        if ($tableDataEnd >= $tableDataStart) {
            $summaryStart = $row;

            // Row: JUMLAH NILAI
            $sumRow = $row;
            $sheet->mergeCells("A{$sumRow}:B{$sumRow}");
            $sheet->setCellValue("A{$sumRow}", 'JUMLAH NILAI');
            $sheet->setCellValue("C{$sumRow}", "=SUM(C{$tableDataStart}:C{$tableDataEnd})");
            $sheet->setCellValue("D{$sumRow}", "=SUM(D{$tableDataStart}:D{$tableDataEnd})");
            $sheet->setCellValue("E{$sumRow}", "=SUM(E{$tableDataStart}:E{$tableDataEnd})");
            $sheet->setCellValue("F{$sumRow}", "=SUM(F{$tableDataStart}:F{$tableDataEnd})");
            $sheet->getRowDimension($sumRow)->setRowHeight(22);
            $row++;

            // Row: RATA-RATA
            $avgRow = $row;
            $sheet->mergeCells("A{$avgRow}:B{$avgRow}");
            $sheet->setCellValue("A{$avgRow}", 'RATA-RATA');
            $sheet->setCellValue("C{$avgRow}", "=ROUND(AVERAGE(C{$tableDataStart}:C{$tableDataEnd}), 1)");
            $sheet->setCellValue("D{$avgRow}", "=ROUND(AVERAGE(D{$tableDataStart}:D{$tableDataEnd}), 1)");
            $sheet->setCellValue("E{$avgRow}", "=ROUND(AVERAGE(E{$tableDataStart}:E{$tableDataEnd}), 1)");
            $sheet->setCellValue("F{$avgRow}", "=ROUND(AVERAGE(F{$tableDataStart}:F{$tableDataEnd}), 1)");
            $sheet->getRowDimension($avgRow)->setRowHeight(22);
            $row++;

            $summaryEnd = $row - 1;

            // Style summary rows
            $sheet->getStyle("A{$summaryStart}:F{$summaryEnd}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 9],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align labels & numbers
            $sheet->getStyle("A{$summaryStart}:B{$summaryEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$summaryStart}:F{$summaryEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Format average cells to 1 decimal place
            $sheet->getStyle("C{$avgRow}:F{$avgRow}")->getNumberFormat()->setFormatCode('0.0');
        }

        // Empty row
        $sheet->getRowDimension($row)->setRowHeight(10);
        $row++;

        // =========================================================
        // KETERANGAN & CATATAN
        // =========================================================
        $ketStart = $row;

        // Header row
        $sheet->mergeCells("A{$row}:B{$row}");
        $sheet->setCellValue("A{$row}", 'Keterangan');
        $sheet->mergeCells("C{$row}:F{$row}");
        $sheet->setCellValue("C{$row}", 'Catatan');
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Content row
        $sakit = $attendance->sakit ?? 0;
        $izin = $attendance->izin ?? 0;
        $alpa = $attendance->alpa ?? 0;
        $note = $attendance->note ?? 'Tetap semangat belajar ya nak!';

        $ketContentRow = $row;

        // Keterangan uses 3 sub-rows inside one merged cell
        $sheet->setCellValue("A{$row}", 'S');
        $sheet->setCellValue("B{$row}", "Sakit = {$sakit}");
        $sheet->mergeCells("C{$row}:F".($row + 2)); // merge catatan across 3 rows
        $sheet->setCellValue("C{$row}", $note);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $sheet->setCellValue("A{$row}", 'I');
        $sheet->setCellValue("B{$row}", "Izin = {$izin}");
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        $sheet->setCellValue("A{$row}", 'A');
        $sheet->setCellValue("B{$row}", "Alpa = {$alpa}");
        $sheet->getRowDimension($row)->setRowHeight(18);

        $ketEnd = $row;

        // Style keterangan section
        $sheet->getStyle("A{$ketStart}:F{$ketEnd}")->applyFromArray([
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'font' => ['size' => 9],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Border for header row
        $sheet->getStyle("A{$ketStart}:F{$ketStart}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        // Border between keterangan and catatan columns
        $sheet->getStyle("A{$ketStart}:B{$ketEnd}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // Border for A column (S/I/A letters)
        $sheet->getStyle("A{$ketContentRow}:A{$ketEnd}")->getBorders()->getRight()->setBorderStyle(Border::BORDER_THIN);
        // Individual row borders in keterangan
        for ($r = $ketContentRow; $r <= $ketEnd; $r++) {
            $sheet->getStyle("A{$r}:B{$r}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        }

        // Center S/I/A letters
        $sheet->getStyle("A{$ketContentRow}:A{$ketEnd}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        // Catatan wrap text + top alignment
        $sheet->getStyle("C{$ketContentRow}:F{$ketEnd}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_TOP)
            ->setWrapText(true);

        $row++;

        // Empty rows before signature
        $sheet->getRowDimension($row)->setRowHeight(10);
        $row++;

        // =========================================================
        // TANDA TANGAN
        // =========================================================
        $signRow = $row;

        // Row 1: "Mengetahui," and "Kota, Tanggal"
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'Mengetahui,');
        $sheet->mergeCells("D{$row}:F{$row}");
        $kabupaten = $school->kabupaten ?? 'Karawang';
        $sheet->setCellValue("D{$row}", $kabupaten.', '.date('d F Y'));
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Row 2: titles
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'Kepala Sekolah '.($school->nama_sekolah ?? 'SMP Harapan Bangsa'));
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'Wali Kelas');
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(9);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // Space for signatures (3 empty rows)
        for ($i = 0; $i < 3; $i++) {
            $sheet->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        // Names row (underlined, bold)
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", $school->nama_kepala_sekolah ?? '_____________________');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(10);
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", $school->nama_wali_kelas ?? '_____________________');
        $sheet->getStyle("D{$row}")->getFont()->setBold(true)->setUnderline(true)->setSize(10);
        $sheet->getRowDimension($row)->setRowHeight(18);
        $row++;

        // NIP row
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'NIP/NIPY: '.($school->nip_kepala_sekolah ?? '-'));
        $sheet->mergeCells("D{$row}:F{$row}");
        $sheet->setCellValue("D{$row}", 'NIP/NIPY: '.($school->nip_wali_kelas ?? '-'));
        $sheet->getStyle("A{$row}:F{$row}")->getFont()->setSize(8);
        $sheet->getRowDimension($row)->setRowHeight(16);

        $signEnd = $row;

        // Center all signature rows
        $sheet->getStyle("A{$signRow}:F{$signEnd}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // =========================================================
        // PAGE SETUP — A4 Portrait, Centered on Page
        // =========================================================
        $sheet->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(1);

        // Center horizontally on printed page
        $sheet->getPageSetup()->setHorizontalCentered(true);

        // Print margins (inches)
        $sheet->getPageMargins()
            ->setTop(0.6)
            ->setBottom(0.4)
            ->setLeft(0.5)
            ->setRight(0.5);

        // Print area
        $sheet->getPageSetup()->setPrintArea("A1:F{$signEnd}");

        // Sheet title
        $sheet->setTitle('RAPORT');
    }
}
