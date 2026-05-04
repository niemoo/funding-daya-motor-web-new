<x-layouts.app title="Tambah Group Part">
    <div class="max-w-xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('part-groups.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">←</a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Tambah Group</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">Tambah kategori group part baru</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <form method="POST" action="{{ route('part-groups.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Group</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Brakes"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                        {{ $errors->has('name') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('name')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan Group
                    </button>
                    <a href="{{ route('part-groups.index') }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
