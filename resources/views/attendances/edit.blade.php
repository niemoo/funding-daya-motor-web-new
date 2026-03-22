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
                        <div class="text-[12px] text-slate-400">Edit dan simpan ulang semua part</div>
                    </div>
                </div>
                <button type="button" onclick="addItemRow()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-600 text-[12px] font-semibold rounded-[8px] transition-colors">
                    + Tambah
                </button>
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

    @push('scripts')
        <script>
            let itemIndex = {{ $attendance->items->count() > 0 ? $attendance->items->count() : 1 }};

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
