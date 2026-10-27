<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Nilai: ') }} {{ $student->name }} ({{ $student->classroom->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>- {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <form action="{{ route('grades.store', $student->id) }}" method="POST">
                    @csrf
                    
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th rowspan="2" class="px-3 py-3 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                    <th rowspan="2" class="px-3 py-3 border border-gray-300 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th colspan="2" class="px-3 py-2 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Formative Harian</th>
                                    <th colspan="2" class="px-3 py-2 border border-gray-300 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Sumatif</th>
                                </tr>
                                <tr>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PR</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">UH</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tertulis</th>
                                    <th class="px-3 py-2 border border-gray-300 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Praktek</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subjects as $index => $subject)
                                    @php
                                        $grade = $grades->get($subject->id);
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-2 border border-gray-300 text-center text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-3 py-2 border border-gray-300 text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][pr]" value="{{ old('grades.'.$subject->id.'.pr', $grade->pr ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][uh]" value="{{ old('grades.'.$subject->id.'.uh', $grade->uh ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][tertulis]" value="{{ old('grades.'.$subject->id.'.tertulis', $grade->tertulis ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                        <td class="px-1 py-1 border border-gray-300">
                                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $subject->id }}][praktek]" value="{{ old('grades.'.$subject->id.'.praktek', $grade->praktek ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-center">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="font-bold text-lg mb-2">Keterangan Kehadiran</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Sakit</label>
                                <input type="number" min="0" name="attendance[sakit]" value="{{ old('attendance.sakit', $attendance->sakit ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Izin</label>
                                <input type="number" min="0" name="attendance[izin]" value="{{ old('attendance.izin', $attendance->izin ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Alpa</label>
                                <input type="number" min="0" name="attendance[alpa]" value="{{ old('attendance.alpa', $attendance->alpa ?? 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="font-bold text-lg mb-2">Catatan Raport</h3>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Catatan Wali Kelas</label>
                                <textarea name="attendance[note]" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('attendance.note', $attendance->note ?? 'Tetap semangat belajar ya nak!') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-200 pt-6">
                        <a href="{{ route('grades.index') }}" class="w-full sm:w-auto text-center px-5 py-2.5 bg-slate-700 hover:bg-slate-800 text-white font-bold rounded-lg shadow-sm text-sm transition-all">
                            Kembali ke Daftar Nilai
                        </a>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            <button type="reset" class="px-5 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-sm text-sm transition-all">
                                Reset
                            </button>
                            <button type="submit" name="action" value="save" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg shadow-md text-sm transition-all">
                                Simpan Nilai
                            </button>
                            <button type="submit" name="action" value="preview" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg shadow-md text-sm transition-all">
                                Simpan & Lihat Raport
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
