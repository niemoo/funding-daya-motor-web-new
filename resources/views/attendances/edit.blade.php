{{-- resources/views/attendances/edit.blade.php --}}
<x-layouts.app title="Edit Kunjungan">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('attendances.show', $attendance) }}"
            class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">
            ←
        </a>
        <div>
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit Kunjungan</h1>
            <p class="text-[13px] text-slate-400 mt-0.5">{{ $attendance->store_name }} ·
                {{ $attendance->attendance_date->locale('id')->isoFormat('D MMMM Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Form Edit Attendance --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="flex items-center gap-2.5 px-5 py-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-[9px] bg-brand-50 flex items-center justify-center text-sm flex-shrink-0">🏪
                </div>
                <div>
                    <div class="text-[14px] font-bold text-slate-800">Data Kunjungan</div>
                    <div class="text-[12px] text-slate-400">Nama toko dan informasi PIC</div>
                </div>
            </div>
            <form method="POST" action="{{ route('attendances.update', $attendance) }}" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- Nama Toko --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Toko</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $attendance->store_name) }}"
                        placeholder="Masukkan nama toko"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('store_name') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('store_name')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama PIC --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama PIC</label>
                    <input type="text" name="person_in_charge_name"
                        value="{{ old('person_in_charge_name', $attendance->person_in_charge_name) }}"
                        placeholder="Masukkan nama PIC"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('person_in_charge_name') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('person_in_charge_name')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- No. Telepon PIC --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">No. Telepon PIC</label>
                    <input type="text" name="person_in_charge_phone"
                        value="{{ old('person_in_charge_phone', $attendance->person_in_charge_phone) }}"
                        placeholder="Masukkan nomor telepon PIC"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('person_in_charge_phone') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('person_in_charge_phone')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex items-center gap-3 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan
                    </button>
                    <a href="{{ route('attendances.show', $attendance) }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>

        {{-- Form Edit Items --}}
        <div id="items" class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-[9px] bg-brand-50 flex items-center justify-center text-sm flex-shrink-0">
                        📦</div>
                    <div>
                        <div class="text-[14px] font-bold text-slate-800">Daftar Part</div>
                        <div class="text-[12px] text-slate-400">Edit manual atau upload Excel</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Upload Excel --}}
                    <label for="excel-upload"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-[12px] font-semibold rounded-[8px] transition-colors cursor-pointer">
                        📂 Upload Excel
                    </label>
                    <input id="excel-upload" type="file" accept=".xlsx,.xls" class="hidden">
                    {{-- Tambah manual --}}
                    <button type="button" onclick="addItemRow()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-600 text-[12px] font-semibold rounded-[8px] transition-colors">
                        + Tambah
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('attendances.items.update', $attendance) }}" id="items-form"
                class="p-5">
                @csrf
                @method('PUT')

                {{-- Header label --}}
                <div class="flex items-center gap-2 mb-2 px-0.5">
                    <div class="flex-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Nomor Part</div>
                    <div class="w-24 text-[10px] font-bold uppercase tracking-wide text-slate-400 text-center">Qty
                    </div>
                    <div class="flex-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Catatan</div>
                    <div class="w-8"></div>
                </div>

                <div id="items-container" class="space-y-2 mb-4">
                    @forelse($attendance->items as $i => $item)
                        <div class="item-row flex items-center gap-2">
                            <div class="flex-1">
                                <input type="text" name="items[{{ $i }}][part_number]"
                                    value="{{ $item->part_number }}" placeholder="Nomor Part"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] font-mono text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                            </div>
                            <div class="w-24">
                                <input type="number" name="items[{{ $i }}][quantity]"
                                    value="{{ $item->quantity }}" placeholder="Qty" min="1"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400 text-center">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="items[{{ $i }}][notes]"
                                    value="{{ $item->notes }}" placeholder="Catatan (opsional)"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                            </div>
                            <button type="button" onclick="removeItemRow(this)"
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-[8px] bg-rose-50 hover:bg-rose-100 text-rose-400 transition-colors text-sm">
                                ✕
                            </button>
                        </div>
                    @empty
                        {{-- Satu row kosong kalau belum ada items --}}
                        <div class="item-row flex items-center gap-2">
                            <div class="flex-1">
                                <input type="text" name="items[0][part_number]" placeholder="Nomor Part"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] font-mono text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                            </div>
                            <div class="w-24">
                                <input type="number" name="items[0][quantity]" placeholder="Qty" min="1"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400 text-center">
                            </div>
                            <div class="flex-1">
                                <input type="text" name="items[0][notes]" placeholder="Catatan (opsional)"
                                    class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                            </div>
                            <button type="button" onclick="removeItemRow(this)"
                                class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-[8px] bg-rose-50 hover:bg-rose-100 text-rose-400 transition-colors text-sm">
                                ✕
                            </button>
                        </div>
                    @endforelse
                </div>

                @error('items')
                    <p class="text-[12px] text-rose-500 mb-3">{{ $message }}</p>
                @enderror

                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan Part
                    </button>
                    <a href="{{ route('attendances.show', $attendance) }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Import Preview --}}
    <div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background:rgba(0,0,0,0.6);backdrop-filter:blur(6px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[85vh] flex flex-col overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 flex-shrink-0">
                <div>
                    <div class="text-[14px] font-bold text-slate-800">Preview Import Excel</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">Cek data sebelum disimpan</div>
                </div>
                <button onclick="closeImportModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors text-sm">
                    ✕
                </button>
            </div>
            <div id="import-modal-content" class="p-5 overflow-y-auto">
                {{-- diisi dinamis oleh JS --}}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let itemIndex = {{ $attendance->items->count() > 0 ? $attendance->items->count() : 1 }};

            // ── Excel Upload Preview ──────────────────────────────────────────────────
            const excelInput = document.getElementById('excel-upload');
            let previewItems = [];

            excelInput.addEventListener('change', function() {
                if (!this.files.length) return;

                const file = this.files[0];
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                // Reset input supaya bisa upload file yang sama lagi
                this.value = '';

                showImportModal('loading');

                fetch('{{ route('attendances.items.import.preview', $attendance) }}', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            showImportModal('error', data.message, data.warnings ?? []);
                            return;
                        }
                        previewItems = data.items;
                        showImportModal('preview', null, data.warnings ?? [], data.items, data.total, data
                            .total_qty);
                    })
                    .catch(() => {
                        showImportModal('error', 'Terjadi kesalahan saat memproses file.');
                    });
            });

            function showImportModal(mode, message = null, warnings = [], items = [], total = 0, totalQty = 0) {
                const modal = document.getElementById('import-modal');
                const content = document.getElementById('import-modal-content');

                if (mode === 'loading') {
                    content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-10 gap-4">
                <div class="w-10 h-10 border-[3px] border-brand-600 border-t-transparent rounded-full animate-spin"></div>
                <div class="text-[13px] text-slate-500 font-medium">Memproses file Excel...</div>
            </div>
        `;
                } else if (mode === 'error') {
                    content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-8 gap-3">
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-2xl">❌</div>
                <div class="text-[14px] font-bold text-slate-800">Import Gagal</div>
                <div class="text-[13px] text-slate-500 text-center">${message ?? 'Terjadi kesalahan.'}</div>
                ${warnings.length > 0 ? `
                                            <div class="w-full mt-2 bg-amber-50 border border-amber-100 rounded-xl p-3 space-y-1">
                                                ${warnings.map(w => `<div class="text-[12px] text-amber-700">⚠️ ${w}</div>`).join('')}
                                            </div>
                                        ` : ''}
                <button onclick="closeImportModal()"
                    class="mt-2 px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                    Tutup
                </button>
            </div>
        `;
                } else if (mode === 'preview') {
                    content.innerHTML = `
            <div class="space-y-4">
                {{-- Header info --}}
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[12px] font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">
                        ${total} item
                    </span>
                    <span class="text-[12px] font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                        Total qty: ${totalQty}
                    </span>
                    ${warnings.length > 0 ? `
                                                <span class="text-[12px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">
                                                    ⚠️ ${warnings.length} baris dilewati
                                                </span>
                                            ` : ''}
                </div>

                {{-- Warnings --}}
                ${warnings.length > 0 ? `
                                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 space-y-1">
                                                <div class="text-[11px] font-bold uppercase tracking-wide text-amber-600 mb-1.5">Baris yang dilewati:</div>
                                                ${warnings.map(w => `<div class="text-[12px] text-amber-700">• ${w}</div>`).join('')}
                                            </div>
                                        ` : ''}

                {{-- Preview table --}}
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left w-8">No</th>
                                <th class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">Nomor Part</th>
                                <th class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-center w-20">Qty</th>
                                <th class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            ${items.map((item, i) => `
                                                        <tr>
                                                            <td class="px-4 py-2.5 text-[12px] text-slate-400">${i + 1}</td>
                                                            <td class="px-4 py-2.5 text-[13px] font-semibold text-slate-800 font-mono">${item.part_number}</td>
                                                            <td class="px-4 py-2.5 text-center text-[13px] font-bold text-brand-600">${item.quantity}</td>
                                                            <td class="px-4 py-2.5 text-[12px] text-slate-500">${item.notes ?? '—'}</td>
                                                        </tr>
                                                    `).join('')}
                        </tbody>
                    </table>
                </div>

                {{-- Info replace --}}
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-[12px] text-blue-700">
                    ℹ️ Data part yang ada saat ini akan <strong>diganti seluruhnya</strong> dengan data dari Excel ini.
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-1">
                    <button onclick="confirmImport()"
                        class="flex-1 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        ✅ Konfirmasi & Simpan
                    </button>
                    <button onclick="closeImportModal()"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        `;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeImportModal() {
                const modal = document.getElementById('import-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            function confirmImport() {
                const btn = document.querySelector('#import-modal button[onclick="confirmImport()"]');
                btn.textContent = 'Menyimpan...';
                btn.disabled = true;

                fetch('{{ route('attendances.items.import.confirm', $attendance) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            items: previewItems
                        }),
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            closeImportModal();
                            alert(data.message ?? 'Gagal menyimpan.');
                        }
                    })
                    .catch(() => {
                        closeImportModal();
                        alert('Terjadi kesalahan saat menyimpan.');
                    });
            }

            document.getElementById('import-modal').addEventListener('click', function(e) {
                if (e.target === this) closeImportModal();
            });

            function addItemRow() {
                const container = document.getElementById('items-container');
                const row = document.createElement('div');
                row.className = 'item-row flex items-center gap-2';
                row.innerHTML = `
                    <div class="flex-1">
                        <input type="text" name="items[${itemIndex}][part_number]" placeholder="Nomor Part"
                            class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] font-mono text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                    </div>
                    <div class="w-24">
                        <input type="number" name="items[${itemIndex}][quantity]" placeholder="Qty" min="1"
                            class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400 text-center">
                    </div>
                    <div class="flex-1">
                        <input type="text" name="items[${itemIndex}][notes]" placeholder="Catatan (opsional)"
                            class="w-full px-3 py-2 bg-slate-50 border-[1.5px] border-slate-200 rounded-[9px] text-[12px] text-slate-800 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
                    </div>
                    <button type="button" onclick="removeItemRow(this)"
                        class="w-8 h-8 flex-shrink-0 flex items-center justify-center rounded-[8px] bg-rose-50 hover:bg-rose-100 text-rose-400 transition-colors text-sm">
                        ✕
                    </button>
                `;
                container.appendChild(row);
                itemIndex++;
            }

            function removeItemRow(btn) {
                const rows = document.querySelectorAll('.item-row');
                if (rows.length <= 1) return; // minimal 1 row
                btn.closest('.item-row').remove();
            }
        </script>
    @endpush

</x-layouts.app>
