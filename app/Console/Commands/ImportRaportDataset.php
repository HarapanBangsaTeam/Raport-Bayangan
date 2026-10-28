<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportRaportDataset extends Command
{
    protected $signature = 'raport:import-dataset';

    protected $description = 'Import students, subjects, and grades from the SMP Harapan Bangsa dataset Excel files';

    public function handle(): int
    {
        $this->info('Starting dataset import...');

        // 1. Ensure Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@harapanbangsa.test'],
            [
                'name' => 'Admin Raport',
                'password' => bcrypt('password'),
            ]
        );
        $this->info("Admin user ensured: {$user->email} / password");

        // 2. School Settings
        $school = SchoolSetting::firstOrCreate(
            ['nama_sekolah' => 'SMP HARAPAN BANGSA'],
            [
                'alamat' => 'Jl. R.E. Martadinata No. 82, RW 09',
                'kelurahan' => 'Nagasari',
                'kabupaten' => 'Karawang Barat, Karawang',
                'nama_kepala_sekolah' => 'Budi Santoso, S.Pd',
                'nip_kepala_sekolah' => '198001012005011001',
                'nama_wali_kelas' => 'Siti Aminah, S.Pd',
                'nip_wali_kelas' => '198502022010012002',
            ]
        );

        // 3. Academic Year & Classroom
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2026/2027'],
            [
                'semester' => 1,
                'is_active' => true,
            ]
        );

        $classroom = Classroom::firstOrCreate(['name' => 'VII']);

        // 4. Subjects (17 official subjects in the exact order of the report layout)
        $subjectsList = [
            1 => ['name' => 'Pendidikan Agama Kristen & Budi Pekerti', 'file' => 'RB PAK.xlsx', 'sheet' => 'KLS 7', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            2 => ['name' => 'Pendidikan Pancasila', 'file' => 'RB PP DAN SM.xlsx', 'sheet' => 'RB PP', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            3 => ['name' => 'Bahasa Indonesia', 'file' => 'RB BAHASA INDONESIA, SENI TEATER, SENI RUPA.xlsx', 'sheet' => 'BAHASA INDONESIA', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            4 => ['name' => 'Matematika', 'file' => 'RB IPA DAN MTK.xlsx', 'sheet' => 'RB MTK', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            5 => ['name' => 'Ilmu Pengetahuan Alam (IPA)', 'file' => 'RB IPA DAN MTK.xlsx', 'sheet' => 'RB IPA', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            6 => ['name' => 'Ilmu Pengetahuan Sosial (IPS)', 'file' => 'RB TIK DAN IPS.xlsx', 'sheet' => 'IPS', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            7 => ['name' => 'Bahasa Inggris', 'file' => 'RB ENGLISH, PRAKARYA, SENI TARI, ENGLISH CONVERSATION.xlsx', 'sheet' => 'SMP.Y7 ENGLISH', 'pr' => 'D', 'uh' => 'E', 'trt' => 'G', 'prk' => 'F'],
            8 => ['name' => 'PJOK', 'file' => 'RB PJOK DAN SUNDA.xlsx', 'sheet' => 'PJOK', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            9 => ['name' => 'Seni Tari', 'file' => 'RB ENGLISH, PRAKARYA, SENI TARI, ENGLISH CONVERSATION.xlsx', 'sheet' => 'SMP.TARI', 'pr' => 'D', 'uh' => 'E', 'trt' => 'G', 'prk' => 'F'],
            10 => ['name' => 'Seni Musik', 'file' => 'RB PP DAN SM.xlsx', 'sheet' => 'RB SM', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            11 => ['name' => 'Seni Teater', 'file' => 'RB BAHASA INDONESIA, SENI TEATER, SENI RUPA.xlsx', 'sheet' => 'SENI TEATER', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            12 => ['name' => 'Seni Rupa', 'file' => 'RB BAHASA INDONESIA, SENI TEATER, SENI RUPA.xlsx', 'sheet' => 'SENI RUPA', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            13 => ['name' => 'Teknik Informatika dan Komunikasi', 'file' => 'RB TIK DAN IPS.xlsx', 'sheet' => 'TIK', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            14 => ['name' => 'Bahasa Sunda', 'file' => 'RB PJOK DAN SUNDA.xlsx', 'sheet' => 'SUNDA', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
            15 => ['name' => 'Bahasa Mandarin', 'file' => 'RB MANDARIN.xlsx', 'sheet' => 'VII', 'pr' => 'F', 'uh' => 'G', 'trt' => 'J', 'prk' => 'K'],
            16 => ['name' => 'Prakarya / Kokurikuler*', 'file' => 'RB ENGLISH, PRAKARYA, SENI TARI, ENGLISH CONVERSATION.xlsx', 'sheet' => 'SMP-.PRAKRYA', 'pr' => 'D', 'uh' => 'E', 'trt' => 'G', 'prk' => 'F'],
            17 => ['name' => 'English Conversation*', 'file' => 'RB ENGLISH, PRAKARYA, SENI TARI, ENGLISH CONVERSATION.xlsx', 'sheet' => 'SMP.CONV', 'pr' => 'D', 'uh' => 'E', 'trt' => 'F', 'prk' => 'G'],
        ];

        $subjectModels = [];
        foreach ($subjectsList as $order => $subMeta) {
            $subjectModels[$order] = Subject::updateOrCreate(
                ['name' => $subMeta['name']],
                [
                    'order_number' => $order,
                    'is_active' => true,
                ]
            );
        }

        // Clean up any extra/orphan subjects not in the 17 official list
        $activeSubjectIds = array_map(fn ($m) => $m->id, $subjectModels);
        Subject::whereNotIn('id', $activeSubjectIds)->delete();

        $this->info('Ensured 17 subjects with correct ordering.');

        // 5. Load Students from Dataset Excel
        $datasetDir = base_path('../Raport bayangan/Dataset Nilai Siswa');
        if (! is_dir($datasetDir)) {
            // fallback if path differs
            $datasetDir = 'C:\laragon\www\SMPHB\Raport bayangan\Dataset Nilai Siswa';
        }

        $studentExcelPath = $datasetDir.DIRECTORY_SEPARATOR.'Nama siswa SMP TP 2026-2027.xlsx';
        if (! file_exists($studentExcelPath)) {
            $this->error("Student Excel file not found at: {$studentExcelPath}");

            return 1;
        }

        $this->info("Reading student list from: {$studentExcelPath}");
        $spreadsheet = IOFactory::load($studentExcelPath);
        $worksheet = $spreadsheet->getActiveSheet();

        $students = [];
        // Rows 3 to 19 (17 students)
        for ($r = 3; $r <= 20; $r++) {
            $noVal = $worksheet->getCell("A{$r}")->getValue();
            $namaVal = trim((string) $worksheet->getCell("B{$r}")->getValue());
            if (empty($namaVal)) {
                continue;
            }

            $nisVal = trim((string) $worksheet->getCell("D{$r}")->getValue());
            $nisnRaw = $worksheet->getCell("E{$r}")->getValue();
            $nisnStr = trim((string) $nisnRaw);
            if (ctype_digit($nisnStr)) {
                $nisnFormatted = str_pad($nisnStr, 10, '0', STR_PAD_LEFT);
            } else {
                $nisnFormatted = $nisnStr;
            }

            $student = Student::updateOrCreate(
                ['name' => $namaVal],
                [
                    'nis' => $nisVal,
                    'nisn' => $nisnFormatted,
                    'classroom_id' => $classroom->id,
                    'academic_year_id' => $academicYear->id,
                    'status' => 'Aktif',
                ]
            );

            // Ensure Attendance
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0,
                    'note' => 'Tetap semangat belajar ya nak!',
                ]
            );

            $students[] = [
                'model' => $student,
                'nama' => $namaVal,
                'nis' => $nisVal,
            ];
        }

        $this->info('Imported/Updated '.count($students).' students.');

        // 6. Load Dataset Grade Workbooks
        $gradeWorkbooks = [];
        foreach ($subjectsList as $subMeta) {
            $fname = $subMeta['file'];
            if (! isset($gradeWorkbooks[$fname])) {
                $fpath = $datasetDir.DIRECTORY_SEPARATOR.$fname;
                if (file_exists($fpath)) {
                    $gradeWorkbooks[$fname] = IOFactory::load($fpath);
                } else {
                    $this->warn("Grade file not found: {$fpath}");
                }
            }
        }

        $cleanVal = function ($v) {
            if ($v === null) {
                return null;
            }
            $s = trim((string) $v);
            if ($s === '' || $s === '-' || $s === 'ꟷ' || strtolower($s) === 'none') {
                return null;
            }
            if (is_numeric($s)) {
                return round((float) $s, 2);
            }

            return null;
        };

        // 7. Extract and Save Grades for Each Student & Subject
        $totalGradesSaved = 0;
        foreach ($students as $st) {
            $studentModel = $st['model'];
            $nama = $st['nama'];
            $nis = $st['nis'];

            foreach ($subjectsList as $order => $subMeta) {
                $fname = $subMeta['file'];
                $sname = $subMeta['sheet'];
                $subjectModel = $subjectModels[$order];

                if (! isset($gradeWorkbooks[$fname])) {
                    continue;
                }

                $wb = $gradeWorkbooks[$fname];
                $ws = $wb->getSheetByName($sname);
                if (! $ws) {
                    continue;
                }

                // Find matching row for student (row 5 to 40)
                $targetRow = null;
                for ($row = 5; $row <= 40; $row++) {
                    $rowText = '';
                    for ($c = 1; $c <= 10; $c++) {
                        $colLetter = Coordinate::stringFromColumnIndex($c);
                        $rowText .= ' '.(string) $ws->getCell("{$colLetter}{$row}")->getValue();
                    }

                    if (($nis !== '' && str_contains($rowText, $nis)) || stripos($rowText, $nama) !== false) {
                        $targetRow = $row;
                        break;
                    }
                }

                $vPr = $targetRow ? $cleanVal($ws->getCell("{$subMeta['pr']}{$targetRow}")->getValue()) : null;
                $vUh = $targetRow ? $cleanVal($ws->getCell("{$subMeta['uh']}{$targetRow}")->getValue()) : null;
                $vTrt = $targetRow ? $cleanVal($ws->getCell("{$subMeta['trt']}{$targetRow}")->getValue()) : null;
                $vPrk = $targetRow ? $cleanVal($ws->getCell("{$subMeta['prk']}{$targetRow}")->getValue()) : null;

                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $studentModel->id,
                        'subject_id' => $subjectModel->id,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'pr' => $vPr,
                        'uh' => $vUh,
                        'tertulis' => $vTrt,
                        'praktek' => $vPrk,
                    ]
                );
                $totalGradesSaved++;
            }
        }

        $this->info("Import completed successfully! Total grades recorded: {$totalGradesSaved}");

        return 0;
    }
}
