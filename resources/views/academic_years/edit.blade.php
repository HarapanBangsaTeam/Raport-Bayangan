<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Tahun Pelajaran & Semester') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Perbarui informasi periode tahun pelajaran atau semester.</p>
            </div>
            <a href="{{ route('academic-years.index') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-bold shadow-sm transition-all">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <form action="{{ route('academic-years.update', $academicYear->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 mb-1">
                            Tahun Pelajaran <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required placeholder="Contoh: 2026/2027"
                            value="{{ old('name', $academicYear->name) }}"
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="semester" class="block text-xs font-bold text-gray-700 mb-1">
                            Semester <span class="text-rose-500">*</span>
                        </label>
                        <select name="semester" id="semester" required
                            class="w-full text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('semester') border-rose-500 @enderror">
                            <option value="1" {{ old('semester', $academicYear->semester) == 1 ? 'selected' : '' }}>Semester 1 (Ganjil)</option>
                            <option value="2" {{ old('semester', $academicYear->semester) == 2 ? 'selected' : '' }}>Semester 2 (Genap)</option>
                        </select>
                        @error('semester')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-semibold text-gray-700">Jadikan periode ini aktif di sistem</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('academic-years.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="py-2 px-5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-lg text-xs shadow-md transition-all flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
