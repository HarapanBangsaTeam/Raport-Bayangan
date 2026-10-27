<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::orderBy('order_number')->orderBy('id')->get();

        return view('subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        $nextOrder = (Subject::max('order_number') ?? 0) + 1;

        return view('subjects.create', compact('nextOrder'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'order_number' => 'nullable|integer',
        ]);

        $validated['order_number'] = $request->filled('order_number')
            ? (int) $request->input('order_number')
            : ((Subject::max('order_number') ?? 0) + 1);
        $validated['is_active'] = $request->boolean('is_active', true);

        Subject::create($validated);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan di urutan terakhir. Perubahan langsung aktif di Preview Raport dan Cetak Excel.');
    }

    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('subjects.edit', $subject);
    }

    public function edit(Subject $subject): View
    {
        return view('subjects.edit', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'order_number' => 'nullable|integer',
        ]);

        $validated['order_number'] = $validated['order_number'] ?? $subject->order_number;
        $validated['is_active'] = $request->boolean('is_active');

        $subject->update($validated);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui. Perubahan langsung aktif di Preview Raport dan Cetak Excel.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $name = $subject->name;
        // Delete related grades if any
        $subject->grades()->delete();
        $subject->delete();

        // Re-sequence remaining subjects so there are no gaps
        $remaining = Subject::orderBy('order_number')->orderBy('id')->get();
        foreach ($remaining as $idx => $s) {
            if ($s->order_number !== $idx + 1) {
                $s->update(['order_number' => $idx + 1]);
            }
        }

        return redirect()->route('subjects.index')->with('success', "Mata pelajaran '{$name}' berhasil dihapus.");
    }

    public function moveUp(Subject $subject): RedirectResponse
    {
        $subjects = Subject::orderBy('order_number')->orderBy('id')->get();
        $currentIndex = $subjects->search(fn ($s) => $s->id === $subject->id);

        if ($currentIndex !== false && $currentIndex > 0) {
            $previousSubject = $subjects->get($currentIndex - 1);

            DB::transaction(function () use ($subject, $previousSubject, $currentIndex, $subjects) {
                foreach ($subjects as $idx => $s) {
                    if ($s->id === $subject->id) {
                        $s->order_number = $currentIndex;
                    } elseif ($s->id === $previousSubject->id) {
                        $s->order_number = $currentIndex + 1;
                    } else {
                        $s->order_number = $idx + 1;
                    }
                    $s->save();
                }
            });

            return redirect()->route('subjects.index')->with('success', "Urutan mata pelajaran '{$subject->name}' berhasil dinaikkan.");
        }

        return redirect()->route('subjects.index');
    }

    public function moveDown(Subject $subject): RedirectResponse
    {
        $subjects = Subject::orderBy('order_number')->orderBy('id')->get();
        $currentIndex = $subjects->search(fn ($s) => $s->id === $subject->id);

        if ($currentIndex !== false && $currentIndex < $subjects->count() - 1) {
            $nextSubject = $subjects->get($currentIndex + 1);

            DB::transaction(function () use ($subject, $nextSubject, $currentIndex, $subjects) {
                foreach ($subjects as $idx => $s) {
                    if ($s->id === $subject->id) {
                        $s->order_number = $currentIndex + 2;
                    } elseif ($s->id === $nextSubject->id) {
                        $s->order_number = $currentIndex + 1;
                    } else {
                        $s->order_number = $idx + 1;
                    }
                    $s->save();
                }
            });

            return redirect()->route('subjects.index')->with('success', "Urutan mata pelajaran '{$subject->name}' berhasil diturunkan.");
        }

        return redirect()->route('subjects.index');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $orders = $request->input('orders', []);

        if (! empty($orders)) {
            DB::transaction(function () use ($orders) {
                $subjects = Subject::whereIn('id', array_keys($orders))->get();

                // Sort subjects by submitted order value, preserving secondary order
                $sorted = $subjects->sortBy(function ($s) use ($orders) {
                    return (int) ($orders[$s->id] ?? $s->order_number);
                })->values();

                // Normalize order numbers 1..N
                foreach ($sorted as $idx => $s) {
                    $s->order_number = $idx + 1;
                    $s->save();
                }
            });

            return redirect()->route('subjects.index')->with('success', 'Urutan mata pelajaran berhasil disimpan! Perubahan langsung diterapkan pada Tabel Preview dan Cetak Excel.');
        }

        return redirect()->route('subjects.index');
    }
}
