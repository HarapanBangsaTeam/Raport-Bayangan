<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'active');
        $activeCount = Student::count();
        $trashedCount = Student::onlyTrashed()->count();

        if ($status === 'trashed') {
            $students = Student::onlyTrashed()
                ->with(['classroom', 'academicYear'])
                ->latest('deleted_at')
                ->paginate(15)
                ->withQueryString();
        } else {
            $students = Student::with(['classroom', 'academicYear'])
                ->paginate(15)
                ->withQueryString();
        }

        return view('students.index', compact('students', 'status', 'activeCount', 'trashedCount'));
    }

    public function create(): View
    {
        $classrooms = Classroom::all();
        $academicYears = AcademicYear::all();

        return view('students.create', compact('classrooms', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'required|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function show(Student $student): RedirectResponse
    {
        return redirect()->route('students.edit', $student);
    }

    public function edit(Student $student): View
    {
        $classrooms = Classroom::all();
        $academicYears = AcademicYear::all();

        return view('students.edit', compact('student', 'classrooms', 'academicYears'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:50',
            'classroom_id' => 'required|exists:classrooms,id',
            'gender' => 'nullable|in:L,P',
            'birthplace' => 'nullable|string|max:100',
            'birthdate' => 'nullable|date',
            'address' => 'nullable|string',
            'academic_year_id' => 'required|exists:academic_years,id',
            'status' => 'required|string|max:50',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $name = $student->name;
        $student->delete();

        return redirect()->route('students.index')->with('success', "Data siswa '{$name}' berhasil dipindahkan ke tempat sampah (soft delete). Data otomatis disembunyikan dari Raport dan Input Nilai.");
    }

    public function restore(string $id): RedirectResponse
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $name = $student->name;
        $student->restore();

        return redirect()->route('students.index', ['status' => 'trashed'])->with('success', "Data siswa '{$name}' berhasil dipulihkan dan kembali aktif di Raport serta Input Nilai.");
    }

    public function forceDelete(string $id): RedirectResponse
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $name = $student->name;

        // Permanently delete associated records
        $student->grades()->delete();
        $student->attendance()->delete();
        $student->forceDelete();

        return redirect()->route('students.index', ['status' => 'trashed'])->with('success', "Data siswa '{$name}' telah dihapus secara permanen dari database.");
    }
}
