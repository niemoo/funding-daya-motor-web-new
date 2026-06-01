<x-layouts.app title="Tambah Part">

    <div class="max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('parts.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">
                ←
            </a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Tambah Part</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">Tambah data part baru ke master</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
            <form method="POST" action="{{ route('parts.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kode Part</label>
                    <input type="text" name="kode_part" value="{{ old('kode_part') }}" placeholder="Contoh: BP-001"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] font-mono text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('kode_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('kode_part')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Deskripsi Part</label>
                    <input type="text" name="deskripsi_part" value="{{ old('deskripsi_part') }}"
                        placeholder="Contoh: Brake Pad Front"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('deskripsi_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('deskripsi_part')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">HET</label>

                    <input type="text" inputmode="numeric" name="het_display" id="het_display"
                        value="{{ old('het') ? number_format(old('het'), 0, ',', '.') : '' }}"
                        placeholder="Contoh: 100.000"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
        {{ $errors->has('het') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">

                    {{-- value asli yang dikirim ke backend --}}
                    <input type="hidden" name="het" id="het" value="{{ old('het') }}">

                    @error('het')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Group</label>
                    <select name="part_group_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all
        {{ $errors->has('part_group_id') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        <option value="">— Pilih Group —</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}"
                                {{ old('part_group_id', $part->part_group_id ?? '') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('part_group_id')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan Part
                    </button>
                    <a href="{{ route('parts.index') }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            const hetDisplay = document.getElementById('het_display');
            const hetHidden = document.getElementById('het');

            hetDisplay.addEventListener('input', function(e) {

                // ambil angka saja
                let value = this.value.replace(/\D/g, '');

                // cegah minus
                if (parseInt(value) < 0) {
                    value = 0;
                }

                // simpan value asli ke hidden input
                hetHidden.value = value;

                // format ribuan
                this.value = new Intl.NumberFormat('id-ID').format(value);
            });
        </script>
    @endpush
</x-layouts.app>
