<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportCardController;
use App\Http\Controllers\SchoolSettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentGradeController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::resource('students', StudentController::class);
    Route::post('/subjects/reorder', [SubjectController::class, 'reorder'])->name('subjects.reorder');
    Route::post('/subjects/{subject}/move-up', [SubjectController::class, 'moveUp'])->name('subjects.move-up');
    Route::post('/subjects/{subject}/move-down', [SubjectController::class, 'moveDown'])->name('subjects.move-down');
    Route::resource('subjects', SubjectController::class);

    // Grades
    Route::get('/grades', [StudentGradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/by-subject', [StudentGradeController::class, 'bySubject'])->name('grades.by-subject');
    Route::post('/grades/by-subject', [StudentGradeController::class, 'storeBySubject'])->name('grades.store-by-subject');
    Route::delete('/grades/destroy-all', [StudentGradeController::class, 'destroyAll'])->name('grades.destroy-all');
    Route::delete('/grades/{student}', [StudentGradeController::class, 'destroyStudentGrades'])->name('grades.destroy-student');
    Route::get('/grades/{student}', [StudentGradeController::class, 'edit'])->name('grades.edit');
    Route::post('/grades/{student}', [StudentGradeController::class, 'store'])->name('grades.store');

    // Reports
    Route::get('/reports', [ReportCardController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-leger', [ReportCardController::class, 'exportLeger'])->name('reports.export-leger');
    Route::get('/reports/print-all', [ReportCardController::class, 'printAll'])->name('reports.print-all');
    Route::get('/reports/{student}/preview', [ReportCardController::class, 'preview'])->name('reports.preview');
    Route::get('/reports/{student}/excel', [ReportCardController::class, 'excel'])->name('reports.excel');

    // Settings
    Route::get('/settings/school', [SchoolSettingController::class, 'edit'])->name('settings.school');
    Route::put('/settings/school', [SchoolSettingController::class, 'update'])->name('settings.school.update');
    Route::resource('academic-years', AcademicYearController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
