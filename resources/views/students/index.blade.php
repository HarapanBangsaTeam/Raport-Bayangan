<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight">
                    {{ __('Daftar Siswa') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Kelola data murid dan status aktif dalam sistem raport.</p>
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('students.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm transition-all text-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>+ Tambah Siswa</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-3 sm:space-y-4">
            
            <!-- Tab Filter Status (Aktif vs Tempat Sampah) -->
            <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
                <a href="{{ route('students.index') }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ ($status ?? 'active') === 'active' ? 'bg-blue-700 text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    <span>Siswa Aktif</span>
                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[11px] rounded-full {{ ($status ?? 'active') === 'active' ? 'bg-blue-800 text-white' : 'bg-gray-200 text-gray-700' }}">
                        {{ $activeCount ?? $students->total() }}
                    </span>
                </a>
                <a href="{{ route('students.index', ['status' => 'trashed']) }}" 
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-bold transition-all {{ ($status ?? 'active') === 'trashed' ? 'bg-rose-700 text-white shadow-xs' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Tempat Sampah</span>
                    @if(($trashedCount ?? 0) > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[11px] rounded-full {{ ($status ?? 'active') === 'trashed' ? 'bg-rose-800 text-white' : 'bg-rose-100 text-rose-800' }}">
                            {{ $trashedCount }}
                        </span>
                    @endif
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-3.5 sm:p-4 rounded-xl shadow-xs">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 text-green-500">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-2.5">
                            <p class="text-xs sm:text-sm font-semibold text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(($status ?? 'active') === 'trashed')
                <div class="bg-amber-50 border-l-4 border-amber-500 p-3.5 rounded-xl text-xs text-amber-900 font-medium">
                    💡 <strong>Mode Tempat Sampah:</strong> Menampilkan siswa yang telah dihapus sementara (soft delete). Siswa di daftar ini <strong>otomatis disembunyikan</strong> dari halaman Raport dan Pengisian Nilai. Anda dapat mengklik <strong>Pulihkan</strong> untuk mengaktifkan kembali, atau <strong>Hapus Permanen</strong> untuk membuang selamanya.
                </div>
            @endif

            <div class="bg-transparent sm:bg-white overflow-hidden sm:shadow-xl rounded-xl sm:rounded-lg sm:border sm:border-gray-200">
                <div class="p-0 sm:p-6">
                    <div class="overflow-x-auto sm:overflow-visible">
                        <table class="block sm:table min-w-full divide-y-0 sm:divide-y divide-gray-200">
                            <thead class="hidden sm:table-header-group bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS / NISN</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
                                    @if(($status ?? 'active') === 'trashed')
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Dihapus Pada</th>
                                    @else
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahun Pelajaran</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    @endif
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block sm:table-row-group sm:divide-y sm:divide-gray-200 space-y-3 sm:space-y-0">
                                @forelse($students as $student)
                                <tr class="block sm:table-row bg-white sm:hover:bg-gray-50 border border-gray-200 sm:border-0 rounded-xl sm:rounded-none p-4 sm:p-0 shadow-2xs sm:shadow-none transition">
                                    <!-- Nama Siswa -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-1 sm:py-4">
                                        <div class="font-bold text-gray-900 text-sm sm:text-base leading-snug">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $student->gender == 'L' ? 'Laki-laki' : ($student->gender == 'P' ? 'Perempuan' : '-') }}
                                        </div>
                                    </td>

                                    <!-- NIS / NISN -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-1 sm:py-4 text-xs sm:text-sm text-gray-700">
                                        <div>NIS: <span class="font-semibold">{{ $student->nis ?? '-' }}</span></div>
                                        <div class="text-xs text-gray-500">NISN: {{ $student->nisn ?? '-' }}</div>
                                    </td>

                                    <!-- Kelas -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-1 sm:py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            {{ $student->classroom->name ?? '-' }}
                                        </span>
                                    </td>

                                    @if(($status ?? 'active') === 'trashed')
                                        <!-- Dihapus Pada -->
                                        <td class="block sm:table-cell px-0 sm:px-6 py-1 sm:py-4 text-xs text-rose-700 font-medium">
                                            <span class="sm:hidden font-semibold text-gray-500">Dihapus: </span>
                                            {{ $student->deleted_at ? $student->deleted_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                    @else
                                        <!-- Tahun Pelajaran -->
                                        <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $student->academicYear->name ?? '-' }}
                                        </td>

                                        <!-- Status -->
                                        <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm">
                                            @if($student->status === 'Aktif')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ $student->status }}
                                                </span>
                                            @endif
                                        </td>
                                    @endif

                                    <!-- Aksi -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-2 sm:py-4 pt-3 sm:pt-4 border-t sm:border-t-0 border-gray-100">
                                        <div class="flex items-center justify-end sm:justify-center gap-2">
                                            @if(($status ?? 'active') === 'trashed')
                                                <!-- Tombol Pulihkan (Restore) -->
                                                <form action="{{ route('students.restore', $student->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-50 border border-emerald-300 text-emerald-800 hover:bg-emerald-700 hover:text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                        Pulihkan
                                                    </button>
                                                </form>

                                                <!-- Tombol Hapus Permanen -->
                                                <form action="{{ route('students.force-delete', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN siswa ini? Seluruh data nilai dan absensi terkait siswa ini akan terhapus selamanya!');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 text-red-800 hover:bg-red-700 hover:text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus Permanen
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('students.edit', $student->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-300 text-indigo-800 hover:bg-indigo-700 hover:text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini? Siswa akan dipindahkan ke tempat sampah dan otomatis tidak muncul di Raport serta Input Nilai.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-300 text-red-800 hover:bg-red-700 hover:text-white rounded-lg text-xs font-bold transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="block sm:table-row bg-white rounded-xl p-8 border border-gray-200 sm:border-0 shadow-2xs sm:shadow-none">
                                    <td colspan="6" class="block sm:table-cell px-6 py-8 text-center text-gray-500">
                                        @if(($status ?? 'active') === 'trashed')
                                            Tidak ada data siswa di tempat sampah.
                                        @else
                                            Belum ada data siswa. Silakan klik tombol "+ Tambah Siswa".
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($students->hasPages())
                        <div class="mt-4 px-2">
                            {{ $students->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
