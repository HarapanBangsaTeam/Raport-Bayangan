<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentGradeController extends Controller
{
    public function index(): View
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $activeSubjectsCount = Subject::where('is_active', true)->count();
        $totalGradesCount = StudentGrade::when($academicYear, fn ($query) => $query->where('academic_year_id', $academicYear->id))->count();

        $students = Student::with(['classroom', 'academicYear', 'grades' => function ($query) use ($academicYear) {
            if ($academicYear) {
                $query->where('academic_year_id', $academicYear->id);
            }
        }])->paginate(20);

        return view('grades.index', compact('students', 'academicYear', 'totalGradesCount', 'activeSubjectsCount'));
    }

    public function bySubject(Request $request): View
    {
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
        $selectedSubjectId = $request->query('subject_id', $subjects->first()?->id);
        $selectedSubject = $subjects->firstWhere('id', $selectedSubjectId) ?? $subjects->first();

        $academicYear = AcademicYear::where('is_active', true)->first();
        $students = Student::with(['classroom'])->orderBy('name')->get();

        $grades = collect();
        if ($selectedSubject && $academicYear) {
            $grades = StudentGrade::where('subject_id', $selectedSubject->id)
                ->where('academic_year_id', $academicYear->id)
                ->get()
                ->keyBy('student_id');
        }

        return view('grades.by_subject', compact('subjects', 'selectedSubject', 'students', 'grades', 'academicYear'));
    }

    public function storeBySubject(Request $request): RedirectResponse
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'grades.*.pr' => 'nullable|numeric|min:0|max:100',
            'grades.*.uh' => 'nullable|numeric|min:0|max:100',
            'grades.*.tertulis' => 'nullable|numeric|min:0|max:100',
            'grades.*.praktek' => 'nullable|numeric|min:0|max:100',
        ]);

        $subjectId = (int) $request->subject_id;
        $academicYear = AcademicYear::where('is_active', true)->first();

        if ($request->has('grades')) {
            foreach ($request->grades as $studentId => $gradeData) {
                StudentGrade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                        'academic_year_id' => $academicYear?->id,
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

        $subject = Subject::find($subjectId);

        return redirect()->route('grades.by-subject', ['subject_id' => $subjectId])
            ->with('success', 'Nilai seluruh siswa untuk mata pelajaran '.($subject->name ?? '').' berhasil disimpan.');
    }

    public function edit(Request $request, Student $student)
    {
        $subjects = Subject::where('is_active', true)->orderBy('order_number')->orderBy('id')->get();
        $academicYearId = $request->query('academic_year_id');
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : (AcademicYear::where('is_active', true)->first() ?? $student->academicYear);

        $grades = StudentGrade::where('student_id', $student->id)
            ->where('academic_year_id', $academicYear->id)
            ->get()
            ->keyBy('subject_id');

        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
            ['sakit' => 0, 'izin' => 0, 'alpa' => 0, 'note' => 'Tetap semangat belajar ya nak!']
        );

        $prevStudent = Student::where('id', '<', $student->id)->orderBy('id', 'desc')->first();
        $nextStudent = Student::where('id', '>', $student->id)->orderBy('id', 'asc')->first();

        return view('grades.edit', compact('student', 'subjects', 'grades', 'attendance', 'academicYear', 'prevStudent', 'nextStudent'));
    }

    public function store(Request $request, Student $student)
    {
        $academicYearId = $request->input('academic_year_id') ?? $request->query('academic_year_id');
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : (AcademicYear::where('is_active', true)->first() ?? $student->academicYear);

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

        if ($request->action === 'save_and_next') {
            $nextStudent = Student::where('id', '>', $student->id)->orderBy('id', 'asc')->first();
            if ($nextStudent) {
                return redirect()->route('grades.edit', $nextStudent->id)
                    ->with('success', 'Nilai '.$student->name.' tersimpan. Melanjutkan ke siswa '.$nextStudent->name.'.');
            }

            return redirect()->route('grades.index')
                ->with('success', 'Nilai '.$student->name.' tersimpan. Anda telah mencapai siswa terakhir.');
        }

        return redirect()->route('grades.edit', $student->id)->with('success', 'Data nilai berhasil disimpan.');
    }

    /**
     * Hapus semua nilai mata pelajaran dan reset absensi untuk seluruh siswa.
     */
    public function destroyAll(Request $request): RedirectResponse
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        // Hapus seluruh nilai pada tahun ajaran aktif (atau seluruh nilai jika tidak ada tahun ajaran aktif)
        $gradeQuery = StudentGrade::query();
        if ($academicYear) {
            $gradeQuery->where('academic_year_id', $academicYear->id);
        }
        $deletedGradesCount = $gradeQuery->delete();

        // Reset data absensi dan catatan raport jika opsi reset_attendance dipilih (default true)
        if ($request->boolean('reset_attendance', true)) {
            $attendanceQuery = Attendance::query();
            if ($academicYear) {
                $attendanceQuery->where('academic_year_id', $academicYear->id);
            }
            $attendanceQuery->delete();
        }

        return redirect()->route('grades.index')->with(
            'success',
            'Semua data nilai'.($request->boolean('reset_attendance', true) ? ' dan absensi' : '').' berhasil dikosongkan. Guru dapat langsung mengisi nilai untuk semester baru.'
        );
    }

    /**
     * Hapus seluruh nilai dan absensi untuk satu siswa tertentu.
     */
    public function destroyStudentGrades(Request $request, Student $student): RedirectResponse
    {
        $academicYearId = $request->input('academic_year_id') ?? $request->query('academic_year_id');
        $academicYear = $academicYearId
            ? AcademicYear::find($academicYearId)
            : (AcademicYear::where('is_active', true)->first() ?? $student->academicYear);

        $gradeQuery = StudentGrade::where('student_id', $student->id);
        if ($academicYear) {
            $gradeQuery->where('academic_year_id', $academicYear->id);
        }
        $gradeQuery->delete();

        if ($request->boolean('reset_attendance', true)) {
            $attendanceQuery = Attendance::where('student_id', $student->id);
            if ($academicYear) {
                $attendanceQuery->where('academic_year_id', $academicYear->id);
            }
            $attendanceQuery->delete();
        }

        return redirect()->route('grades.edit', $student->id)->with(
            'success',
            'Data nilai untuk siswa '.$student->name.' berhasil dikosongkan.'
        );
    }
}
