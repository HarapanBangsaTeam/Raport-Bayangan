<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Mata Pelajaran') }}
            </h2>
            <a href="{{ route('subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-2xl mx-auto px-3 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:shadow-xl rounded-xl sm:rounded-lg p-4 sm:p-6 border border-gray-200 sm:border-0">
                <form action="{{ route('subjects.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Nama Mapel -->
                    <div>
                        <x-input-label for="name" :value="__('Nama Mata Pelajaran *')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Contoh: Matematika" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Kode Mapel -->
                    <div>
                        <x-input-label for="code" :value="__('Kode Mapel (Opsional)')" />
                        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full uppercase" :value="old('code')" placeholder="Contoh: MTK" />
                        <x-input-error class="mt-2" :messages="$errors->get('code')" />
                    </div>

                    <input type="hidden" name="order_number" value="{{ $nextOrder }}">

                    <!-- Status Aktif -->
                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900 font-medium">
                            Aktif (Tampilkan dalam Raport dan Input Nilai)
                        </label>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Batal
                        </a>
                        <x-primary-button>
                            {{ __('Simpan Mata Pelajaran') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
