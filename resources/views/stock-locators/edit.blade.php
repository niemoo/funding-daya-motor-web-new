<x-layouts.app title="Edit Stock Locator">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('stock-locators.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">←</a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit Stock Locator</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">{{ $stockLocator->kode_part }} —
                    {{ $stockLocator->branch->kode_cabang ?? '—' }}</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <form method="POST" action="{{ route('stock-locators.update', $stockLocator) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Cabang --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Cabang</label>
                    <select name="branch_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all
                        {{ $errors->has('branch_id') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        <option value="">— Pilih Cabang —</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', $stockLocator->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->kode_cabang }} — {{ $branch->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode Part --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kode Part</label>
                    <input type="text" name="kode_part" value="{{ old('kode_part', $stockLocator->kode_part) }}"
                        placeholder="Contoh: 50500KEV880"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] font-mono text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('kode_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('kode_part')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Group --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Group</label>
                    <select name="part_group_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all
                        {{ $errors->has('part_group_id') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        <option value="">— Pilih Group —</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}"
                                {{ old('part_group_id', $stockLocator->part_group_id) == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('part_group_id')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi Stock --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Lokasi Stock</label>
                    <input type="text" name="lokasi_stock"
                        value="{{ old('lokasi_stock', $stockLocator->lokasi_stock) }}" placeholder="Contoh: A1 POJOK"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] font-mono text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('lokasi_stock') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('lokasi_stock')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jumlah & Nilai Stock --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Jumlah</label>
                        <input type="number" name="jumlah" value="{{ old('jumlah', $stockLocator->jumlah) }}"
                            placeholder="0" min="0" step="0.01"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                            {{ $errors->has('jumlah') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        @error('jumlah')
                            <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nilai Stock (Rp)</label>
                        <input type="number" name="nilai_stock"
                            value="{{ old('nilai_stock', $stockLocator->nilai_stock) }}" placeholder="0" min="0"
                            step="0.01"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                            {{ $errors->has('nilai_stock') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        @error('nilai_stock')
                            <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Preview Total --}}
                <div class="bg-brand-50 border border-brand-100 rounded-xl px-4 py-3 flex items-center justify-between">
                    <span class="text-[12px] font-semibold text-brand-600">Total Nilai</span>
                    <span id="preview-total" class="text-[14px] font-bold text-brand-700">Rp 0</span>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('stock-locators.index') }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const jumlahInput = document.querySelector('input[name="jumlah"]');
            const nilaiInput = document.querySelector('input[name="nilai_stock"]');
            const previewTotal = document.getElementById('preview-total');

            function updateTotal() {
                const jumlah = parseFloat(jumlahInput.value) || 0;
                const nilai = parseFloat(nilaiInput.value) || 0;
                const total = jumlah * nilai;
                previewTotal.textContent = 'Rp ' + total.toLocaleString('id-ID', {
                    minimumFractionDigits: 2
                });
            }

            jumlahInput.addEventListener('input', updateTotal);
            nilaiInput.addEventListener('input', updateTotal);
            updateTotal();
        </script>
    @endpush
</x-layouts.app>
