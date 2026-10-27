<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-bold text-xl sm:text-2xl text-gray-900 leading-tight">
                    {{ __('Mata Pelajaran') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Atur daftar dan urutan mata pelajaran untuk tampilan Raport & Excel.</p>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 w-full sm:w-auto">
                <button type="submit" form="reorder-form" id="btn-save-order" class="btn-save-order inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2.5 sm:py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm transition-all text-center">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    <span>Simpan Urutan</span>
                </button>
                <a href="{{ route('subjects.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2.5 sm:py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm transition-all text-center">
                    <span>+ Tambah Mapel</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4 sm:py-8">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 space-y-3 sm:space-y-4">
            <!-- Form untuk simpan urutan (HTML5 form linking) -->
            <form id="reorder-form" action="{{ route('subjects.reorder') }}" method="POST" class="hidden">
                @csrf
            </form>

            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-3.5 sm:p-4 rounded-xl shadow-xs">
                    <div class="flex items-center justify-between gap-2">
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
                        <a href="{{ route('reports.index') }}" class="text-xs font-bold text-green-800 hover:text-green-950 underline shrink-0">
                            Buka Raport &rarr;
                        </a>
                    </div>
                </div>
            @endif

            <!-- Notifikasi perubahan urutan yang belum disimpan (Sticky on mobile) -->
            <div id="dirty-order-alert" class="hidden sticky top-2 z-20 bg-amber-50 border-l-4 border-amber-500 p-3 rounded-xl shadow-md transition-all">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 text-amber-900 text-xs font-semibold">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Urutan diubah. Klik <strong>"Simpan Perubahan"</strong> di atas.</span>
                    </div>
                    <button type="submit" form="reorder-form" class="sm:hidden px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-2xs shrink-0">
                        Simpan
                    </button>
                </div>
            </div>

            <div class="bg-transparent sm:bg-white overflow-hidden sm:shadow-xl rounded-xl sm:rounded-lg sm:border sm:border-gray-200">
                <div class="p-0 sm:p-6">
                    <div class="overflow-x-auto sm:overflow-visible">
                        <table class="block sm:table min-w-full divide-y-0 sm:divide-y divide-gray-200">
                            <thead class="hidden sm:table-header-group bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Urutan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Mapel</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="subjects-tbody" class="block sm:table-row-group sm:divide-y sm:divide-gray-200 space-y-3 sm:space-y-0">
                                @forelse($subjects as $index => $subject)
                                <tr class="subject-row block sm:table-row bg-white sm:hover:bg-gray-50 border border-gray-200 sm:border-0 rounded-xl sm:rounded-none p-3.5 sm:p-0 shadow-2xs sm:shadow-none transition duration-150" data-id="{{ $subject->id }}">
                                    <!-- Kolom Urutan (Header Card di Mobile) -->
                                    <td class="block sm:table-cell px-0 sm:px-4 py-0 sm:py-3 border-b sm:border-b-0 border-gray-100 pb-2.5 sm:pb-3">
                                        <div class="flex items-center justify-between sm:justify-center gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="order-badge inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 border border-blue-200 font-extrabold text-blue-900 text-xs sm:text-sm shadow-2xs select-none">
                                                    {{ $subject->order_number }}
                                                </span>
                                                <span class="sm:hidden text-xs font-semibold text-gray-500">
                                                    Posisi ke-<span class="mobile-order-num">{{ $subject->order_number }}</span>
                                                </span>
                                            </div>

                                            <!-- Tombol Naik & Turun -->
                                            <div class="flex items-center gap-1 bg-slate-100 border border-slate-200 p-0.5 rounded-lg shadow-2xs">
                                                <button type="button" 
                                                    onclick="moveRowUp(this)" 
                                                    title="Pindahkan ke atas (Naik)"
                                                    @if($loop->first) disabled class="btn-move-up p-1.5 sm:p-1 text-slate-300 cursor-not-allowed" @else class="btn-move-up p-1.5 sm:p-1 text-slate-700 hover:text-blue-700 hover:bg-white rounded transition shadow-2xs" @endif>
                                                    <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                </button>
                                                <button type="button" 
                                                    onclick="moveRowDown(this)" 
                                                    title="Pindahkan ke bawah (Turun)"
                                                    @if($loop->last) disabled class="btn-move-down p-1.5 sm:p-1 text-slate-300 cursor-not-allowed" @else class="btn-move-down p-1.5 sm:p-1 text-slate-700 hover:text-blue-700 hover:bg-white rounded transition shadow-2xs" @endif>
                                                    <svg class="w-4 h-4 sm:w-3.5 sm:h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Input tersembunyi untuk form submission -->
                                            <input type="hidden" form="reorder-form" name="orders[{{ $subject->id }}]" value="{{ $subject->order_number }}" class="order-input">
                                        </div>
                                    </td>

                                    <!-- Kolom Nama Mapel (Body Card di Mobile) -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-2.5 sm:py-4">
                                        <div class="font-bold text-gray-900 text-sm sm:text-base leading-snug">{{ $subject->name }}</div>
                                        <div class="sm:hidden flex flex-wrap items-center gap-2 mt-1.5 text-xs text-gray-500">
                                            <span>Kode: <strong class="text-gray-700">{{ $subject->code ?? '-' }}</strong></span>
                                            <span>•</span>
                                            @if($subject->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">
                                                    Non-Aktif
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Kolom Kode Mapel (Desktop Only) -->
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $subject->code ?? '-' }}
                                    </td>

                                    <!-- Kolom Status (Desktop Only) -->
                                    <td class="hidden sm:table-cell px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($subject->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Kolom Aksi (Footer Card di Mobile) -->
                                    <td class="block sm:table-cell px-0 sm:px-6 py-2 sm:py-4 pt-2.5 sm:pt-4 border-t sm:border-t-0 border-gray-100">
                                        <div class="flex items-center justify-end sm:justify-center gap-2">
                                            <a href="{{ route('subjects.edit', $subject->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
                                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini? Seluruh nilai terkait mapel ini juga akan terhapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 hover:bg-red-600 hover:text-white rounded-lg text-xs font-bold transition-all shadow-2xs">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="block sm:table-row bg-white rounded-xl p-8 border border-gray-200 sm:border-0 shadow-2xs sm:shadow-none">
                                    <td colspan="5" class="block sm:table-cell px-6 py-8 text-center text-gray-500">
                                        Belum ada data mata pelajaran. Silakan klik tombol "+ Tambah Mapel".
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($subjects->isNotEmpty())
                <div class="px-4 py-3 bg-white sm:bg-gray-50 border-t border-gray-200 rounded-b-xl sm:rounded-b-lg text-xs text-gray-500 flex flex-col sm:flex-row items-center justify-between gap-1">
                    <span>💡 Gunakan panah ⬆ / ⬇ untuk menaikkan atau menurunkan posisi mata pelajaran, lalu klik <strong>Simpan Urutan</strong>.</span>
                    <span class="text-gray-400 font-medium shrink-0">Total: {{ $subjects->count() }} Mata Pelajaran</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function updateRowIndexes() {
            const rows = document.querySelectorAll('#subjects-tbody tr.subject-row');
            rows.forEach((row, idx) => {
                const badge = row.querySelector('.order-badge');
                if (badge) {
                    badge.textContent = idx + 1;
                }
                const mobileNum = row.querySelector('.mobile-order-num');
                if (mobileNum) {
                    mobileNum.textContent = idx + 1;
                }
                const orderInput = row.querySelector('.order-input');
                if (orderInput) {
                    orderInput.value = idx + 1;
                }
                const upBtn = row.querySelector('.btn-move-up');
                const downBtn = row.querySelector('.btn-move-down');
                if (upBtn) {
                    if (idx === 0) {
                        upBtn.disabled = true;
                        upBtn.className = 'btn-move-up p-1.5 sm:p-1 text-slate-300 cursor-not-allowed';
                    } else {
                        upBtn.disabled = false;
                        upBtn.className = 'btn-move-up p-1.5 sm:p-1 text-slate-700 hover:text-blue-700 hover:bg-white rounded transition shadow-2xs';
                    }
                }
                if (downBtn) {
                    if (idx === rows.length - 1) {
                        downBtn.disabled = true;
                        downBtn.className = 'btn-move-down p-1.5 sm:p-1 text-slate-300 cursor-not-allowed';
                    } else {
                        downBtn.disabled = false;
                        downBtn.className = 'btn-move-down p-1.5 sm:p-1 text-slate-700 hover:text-blue-700 hover:bg-white rounded transition shadow-2xs';
                    }
                }
            });
            markDirty();
        }

        function moveRowUp(btn) {
            const row = btn.closest('tr');
            const prev = row.previousElementSibling;
            if (prev && prev.classList.contains('subject-row')) {
                row.parentNode.insertBefore(row, prev);
                highlightRow(row);
                updateRowIndexes();
            }
        }

        function moveRowDown(btn) {
            const row = btn.closest('tr');
            const next = row.nextElementSibling;
            if (next && next.classList.contains('subject-row')) {
                row.parentNode.insertBefore(next, row);
                highlightRow(row);
                updateRowIndexes();
            }
        }

        function highlightRow(row) {
            row.classList.add('bg-blue-50', 'ring-2', 'ring-blue-300');
            setTimeout(() => {
                row.classList.remove('bg-blue-50', 'ring-2', 'ring-blue-300');
            }, 600);
        }

        function markDirty() {
            const saveBtn = document.getElementById('btn-save-order');
            if (saveBtn) {
                saveBtn.classList.remove('bg-blue-700', 'hover:bg-blue-800');
                saveBtn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'ring-2', 'ring-emerald-400', 'ring-offset-2', 'animate-pulse');
                saveBtn.innerHTML = `
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Perubahan</span>
                `;
            }
            const dirtyAlert = document.getElementById('dirty-order-alert');
            if (dirtyAlert) {
                dirtyAlert.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Feedback visual saat proses simpan berlangsung
            document.getElementById('reorder-form')?.addEventListener('submit', function () {
                const saveBtn = document.getElementById('btn-save-order');
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.classList.remove('animate-pulse');
                    saveBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menyimpan...`;
                }
                const dirtyAlertBtn = document.querySelector('#dirty-order-alert button');
                if (dirtyAlertBtn) {
                    dirtyAlertBtn.disabled = true;
                    dirtyAlertBtn.textContent = 'Menyimpan...';
                }
            });
        });
    </script>
</x-app-layout>
