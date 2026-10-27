<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalSubjects = Subject::count();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return view('dashboard', compact('totalStudents', 'totalSubjects', 'activeYear'));
    }
}
