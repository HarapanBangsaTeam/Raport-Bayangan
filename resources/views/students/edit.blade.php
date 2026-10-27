<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Data Siswa') }}: <span class="text-indigo-600">{{ $student->name }}</span>
            </h2>
            <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('students.update', $student->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Siswa -->
                        <div class="md:col-span-2">
                            <x-input-label for="name" :value="__('Nama Lengkap Siswa *')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full uppercase" :value="old('name', $student->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- NIS -->
                        <div>
                            <x-input-label for="nis" :value="__('Nomor Induk Siswa (NIS)')" />
                            <x-text-input id="nis" name="nis" type="text" class="mt-1 block w-full" :value="old('nis', $student->nis)" />
                            <x-input-error class="mt-2" :messages="$errors->get('nis')" />
                        </div>

                        <!-- NISN -->
                        <div>
                            <x-input-label for="nisn" :value="__('NISN')" />
                            <x-text-input id="nisn" name="nisn" type="text" class="mt-1 block w-full" :value="old('nisn', $student->nisn)" />
                            <x-input-error class="mt-2" :messages="$errors->get('nisn')" />
                        </div>

                        <!-- Kelas -->
                        <div>
                            <x-input-label for="classroom_id" :value="__('Kelas *')" />
                            <select id="classroom_id" name="classroom_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id', $student->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('classroom_id')" />
                        </div>

                        <!-- Tahun Pelajaran -->
                        <div>
                            <x-input-label for="academic_year_id" :value="__('Tahun Pelajaran *')" />
                            <select id="academic_year_id" name="academic_year_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Tahun Pelajaran --</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}" {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
                                        {{ $year->name }} ({{ $year->semester }}) {{ $year->is_active ? ' - [Aktif]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('academic_year_id')" />
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <x-input-label for="gender" :value="__('Jenis Kelamin')" />
                            <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                        </div>

                        <!-- Status -->
                        <div>
                            <x-input-label for="status" :value="__('Status Siswa *')" />
                            <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Aktif" {{ old('status', $student->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Mutasi" {{ old('status', $student->status) == 'Mutasi' ? 'selected' : '' }}>Mutasi</option>
                                <option value="Lulus" {{ old('status', $student->status) == 'Lulus' ? 'selected' : '' }}>Lulus</option>
                                <option value="Non-Aktif" {{ old('status', $student->status) == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('status')" />
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <x-input-label for="birthplace" :value="__('Tempat Lahir')" />
                            <x-text-input id="birthplace" name="birthplace" type="text" class="mt-1 block w-full" :value="old('birthplace', $student->birthplace)" />
                            <x-input-error class="mt-2" :messages="$errors->get('birthplace')" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <x-input-label for="birthdate" :value="__('Tanggal Lahir')" />
                            <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full" :value="old('birthdate', $student->birthdate)" />
                            <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('Alamat')" />
                            <textarea id="address" name="address" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $student->address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('students.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Perubahan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
