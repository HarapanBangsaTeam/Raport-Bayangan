<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $academicYears = AcademicYear::withCount(['grades', 'attendances'])
            ->orderBy('name', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true);

        return view('academic_years.index', compact('academicYears', 'activeYear'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $isActive = $request->boolean('is_active');

        // Jika dipilih aktif atau jika belum ada tahun ajaran sama sekali
        if ($isActive || AcademicYear::count() === 0) {
            AcademicYear::query()->update(['is_active' => false]);
            $validated['is_active'] = true;
        } else {
            $validated['is_active'] = false;
        }

        $newYear = AcademicYear::create($validated);

        return redirect()->route('academic-years.index')->with(
            'success',
            'Tahun Pelajaran & Semester '.$newYear->full_period.' berhasil ditambahkan.'
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear): View
    {
        return view('academic_years.edit', compact('academicYear'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->boolean('is_active')) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
            $validated['is_active'] = true;
        }

        $academicYear->update($validated);

        return redirect()->route('academic-years.index')->with(
            'success',
            'Data Tahun Pelajaran '.$academicYear->name.' Semester '.$academicYear->semester.' berhasil diperbarui.'
        );
    }

    /**
     * Jadikan tahun pelajaran ini sebagai periode aktif di seluruh sistem.
     */
    public function setActive(AcademicYear $academicYear): RedirectResponse
    {
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with(
            'success',
            'Tahun Pelajaran '.$academicYear->full_period.' sekarang AKTIF. Input nilai dan raport otomatis beralih ke periode ini.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        if ($academicYear->is_active) {
            return back()->with('error', 'Tahun Pelajaran yang sedang aktif tidak dapat dihapus. Aktifkan periode lain terlebih dahulu.');
        }

        $gradesCount = $academicYear->grades()->count();
        if ($gradesCount > 0) {
            return back()->with('error', "Periode ini memiliki {$gradesCount} data nilai tersimpan. Untuk menjaga integritas data riwayat raport, periode ini tidak dapat dihapus.");
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')->with(
            'success',
            'Tahun Pelajaran berhasil dihapus.'
        );
    }
}
