<x-layouts.app title="Edit Part">

    <div class="max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('parts.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">
                ←
            </a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit Part</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">{{ $part->kode_part }} — {{ $part->deskripsi_part }}</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 space-y-5">
            <form method="POST" action="{{ route('parts.update', $part) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Kode Part</label>
                    <input type="text" name="kode_part" value="{{ old('kode_part', $part->kode_part) }}"
                        placeholder="Contoh: BP-001"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] font-mono text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('kode_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('kode_part')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Deskripsi Part</label>
                    <input type="text" name="deskripsi_part"
                        value="{{ old('deskripsi_part', $part->deskripsi_part) }}" placeholder="Contoh: Brake Pad Front"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('deskripsi_part') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('deskripsi_part')
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
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('parts.index') }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
