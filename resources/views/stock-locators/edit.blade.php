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

                {{-- Kode Part + Autocomplete --}}
                <div class="relative">
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kode Part</label>
                    <input type="text" id="kode-part-input" name="kode_part"
                        value="{{ old('kode_part', $stockLocator->kode_part) }}" placeholder="Ketik minimal 3 huruf..."
                        autocomplete="off"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] font-mono text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('kode_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    <div id="part-suggestions"
                        class="hidden absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden max-h-60 overflow-y-auto">
                    </div>
                    @error('kode_part')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Hidden part_group_id --}}
                <input type="hidden" id="part-group-id" name="part_group_id"
                    value="{{ old('part_group_id', $stockLocator->part_group_id) }}">

                {{-- Group — read only, auto dari part --}}
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Group</label>
                    <div id="group-display"
                        class="w-full px-3.5 py-2.5 border-[1.5px] border-slate-200 rounded-[10px] text-[13px] min-h-[42px] flex items-center
                        {{ $stockLocator->group ? 'bg-slate-50 font-semibold text-slate-800' : 'bg-slate-100 text-slate-400' }}">
                        {{ $stockLocator->group?->name ?? '— otomatis dari part —' }}
                    </div>
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
                            placeholder="0" min="0" step="1"
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
                            step="1"
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
            // ── Total Preview ──────────────────────────────────────────────────
            const jumlahInput = document.querySelector('input[name="jumlah"]');
            const nilaiInput = document.querySelector('input[name="nilai_stock"]');
            const previewTotal = document.getElementById('preview-total');

            function updateTotal() {
                const jumlah = parseInt(jumlahInput.value) || 0;
                const nilai = parseInt(nilaiInput.value) || 0;
                previewTotal.textContent = 'Rp ' + (jumlah * nilai).toLocaleString('id-ID');
            }
            jumlahInput.addEventListener('input', updateTotal);
            nilaiInput.addEventListener('input', updateTotal);
            updateTotal();

            // ── Autocomplete Kode Part ─────────────────────────────────────────
            const kodePartInput = document.getElementById('kode-part-input');
            const partSuggestions = document.getElementById('part-suggestions');
            const partGroupIdInput = document.getElementById('part-group-id');
            const groupDisplay = document.getElementById('group-display');

            let debounceTimer = null;

            kodePartInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                partGroupIdInput.value = '';
                groupDisplay.textContent = '— otomatis dari part —';
                groupDisplay.className =
                    'w-full px-3.5 py-2.5 bg-slate-100 border-[1.5px] border-slate-200 rounded-[10px] text-[13px] text-slate-400 min-h-[42px] flex items-center';

                if (keyword.length < 3) {
                    partSuggestions.classList.add('hidden');
                    return;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => searchParts(keyword), 350);
            });

            async function searchParts(keyword) {
                try {
                    const response = await fetch(`{{ route('parts.autocomplete') }}?q=${encodeURIComponent(keyword)}`);
                    const data = await response.json();
                    if (!data.length) {
                        partSuggestions.classList.add('hidden');
                        return;
                    }
                    renderSuggestions(data);
                } catch (err) {
                    partSuggestions.classList.add('hidden');
                }
            }

            function renderSuggestions(parts) {
                partSuggestions.innerHTML = parts.map(part => `
                    <button type="button"
                        onclick="selectPart('${esc(part.kode_part)}', ${part.part_group_id ?? 'null'}, '${esc(part.group_name ?? '')}')"
                        class="w-full flex items-center gap-3 px-4 py-3 hover:bg-brand-50 transition-colors text-left border-b border-slate-50 last:border-0">
                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-semibold text-slate-800 font-mono">${esc(part.kode_part)}</div>
                            <div class="text-[11px] text-slate-400 truncate">${esc(part.deskripsi_part)}</div>
                        </div>
                        ${part.group_name ? `<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-50 text-brand-600 flex-shrink-0">${esc(part.group_name)}</span>` : ''}
                    </button>
                `).join('');
                partSuggestions.classList.remove('hidden');
            }

            function selectPart(kodePart, partGroupId, groupName) {
                kodePartInput.value = kodePart;
                partGroupIdInput.value = partGroupId ?? '';
                partSuggestions.classList.add('hidden');

                if (groupName) {
                    groupDisplay.textContent = groupName;
                    groupDisplay.className =
                        'w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-[10px] text-[13px] font-semibold text-slate-800 min-h-[42px] flex items-center';
                } else {
                    groupDisplay.textContent = '— tidak ada group —';
                }
            }

            function esc(str) {
                if (!str) return '';
                return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(
                    /'/g, '&#039;');
            }

            document.addEventListener('click', function(e) {
                if (!kodePartInput.contains(e.target) && !partSuggestions.contains(e.target)) {
                    partSuggestions.classList.add('hidden');
                }
            });
        </script>
    @endpush
</x-layouts.app>
