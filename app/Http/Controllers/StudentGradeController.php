<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentGradeController extends Controller
{
    public function index()
    {
        $students = Student::with(['classroom', 'academicYear'])->paginate(20);

        return view('grades.index', compact('students'));
    }

    public function edit(Student $student)
    {
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();

        $grades = StudentGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get()
            ->keyBy('subject_id');

        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
            ['sakit' => 0, 'izin' => 0, 'alpa' => 0, 'note' => 'Tetap semangat belajar ya nak!']
        );

        return view('grades.edit', compact('student', 'subjects', 'grades', 'attendance', 'academicYear'));
    }

    public function store(Request $request, Student $student)
    {
        $academicYear = $student->academicYear ?? AcademicYear::where('is_active', true)->first();

        // Validasi
        $request->validate([
            'grades.*.pr' => 'nullable|numeric|min:0|max:100',
            'grades.*.uh' => 'nullable|numeric|min:0|max:100',
            'grades.*.tertulis' => 'nullable|numeric|min:0|max:100',
            'grades.*.praktek' => 'nullable|numeric|min:0|max:100',

            'attendance.sakit' => 'nullable|integer|min:0',
            'attendance.izin' => 'nullable|integer|min:0',
            'attendance.alpa' => 'nullable|integer|min:0',
            'attendance.note' => 'nullable|string',
        ]);

        // Simpan nilai
        if ($request->has('grades')) {
            foreach ($request->grades as $subjectId => $gradeData) {
                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'pr' => (isset($gradeData['pr']) && $gradeData['pr'] !== '') ? $gradeData['pr'] : null,
                        'uh' => (isset($gradeData['uh']) && $gradeData['uh'] !== '') ? $gradeData['uh'] : null,
                        'tertulis' => (isset($gradeData['tertulis']) && $gradeData['tertulis'] !== '') ? $gradeData['tertulis'] : null,
                        'praktek' => (isset($gradeData['praktek']) && $gradeData['praktek'] !== '') ? $gradeData['praktek'] : null,
                    ]
                );
            }
        }

        // Simpan kehadiran
        if ($request->has('attendance')) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'sakit' => $request->attendance['sakit'] ?? 0,
                    'izin' => $request->attendance['izin'] ?? 0,
                    'alpa' => $request->attendance['alpa'] ?? 0,
                    'note' => $request->attendance['note'] ?? null,
                ]
            );
        }

        if ($request->action == 'preview') {
            return redirect()->route('reports.preview', $student->id);
        }

        return redirect()->route('grades.edit', $student->id)->with('success', 'Data nilai berhasil disimpan.');
    }
}
