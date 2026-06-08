<x-layouts.app title="Input Supply">

    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('attendances.show', $attendance) }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">←</a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Input Supply</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">
                    {{ $attendance->generalStore?->name ?? $attendance->store_name }} ·
                    {{ $attendance->attendance_date->locale('id')->isoFormat('D MMMM Y') }}
                </p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <div class="text-[14px] font-bold text-slate-800">Daftar Supply Part</div>
                <div class="text-[12px] text-slate-400 mt-0.5">
                    Isi jumlah part yang dapat dipenuhi. Invoice akan menggunakan jumlah supply ini.
                </div>
            </div>

            <form method="POST" action="{{ route('attendances.supply.update', $attendance) }}">
                @csrf
                @method('PUT')

                @if ($supplies->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <div class="text-2xl mb-2">📦</div>
                        <div class="text-[13px] font-semibold text-slate-500">Tidak ada items untuk di-supply</div>
                        <div class="text-[12px] text-slate-400 mt-1">Tambahkan items terlebih dahulu di halaman edit
                        </div>
                    </div>
                @else
                    {{-- Header --}}
                    <div class="grid grid-cols-12 gap-3 px-5 py-2.5 bg-slate-50 border-b border-slate-100">
                        <div class="col-span-4 text-[10px] font-bold uppercase tracking-wide text-slate-400">Kode Part
                        </div>
                        <div class="col-span-3 text-[10px] font-bold uppercase tracking-wide text-slate-400">Deskripsi
                        </div>
                        <div
                            class="col-span-2 text-[10px] font-bold uppercase tracking-wide text-slate-400 text-center">
                            Request</div>
                        <div
                            class="col-span-2 text-[10px] font-bold uppercase tracking-wide text-slate-400 text-center">
                            Supply</div>
                        <div class="col-span-1"></div>
                    </div>

                    <div class="divide-y divide-slate-50 px-5 py-3 space-y-3">
                        @foreach ($supplies as $i => $supply)
                            <input type="hidden" name="supplies[{{ $i }}][kode_part]"
                                value="{{ $supply['kode_part'] }}">
                            <input type="hidden" name="supplies[{{ $i }}][quantity_requested]"
                                value="{{ $supply['quantity_requested'] }}">

                            <div class="grid grid-cols-12 gap-3 items-center pt-3 first:pt-0">
                                {{-- Kode Part --}}
                                <div class="col-span-4">
                                    <div class="text-[13px] font-semibold text-slate-800 font-mono">
                                        {{ $supply['kode_part'] }}</div>
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-span-3">
                                    <div class="text-[12px] text-slate-500 truncate">{{ $supply['deskripsi_part'] }}
                                    </div>
                                </div>

                                {{-- Qty Requested --}}
                                <div class="col-span-2 text-center">
                                    <span
                                        class="inline-flex items-center justify-center text-[13px] font-bold text-slate-600 bg-slate-100 rounded-lg w-10 h-8">
                                        {{ $supply['quantity_requested'] }}
                                    </span>
                                </div>

                                {{-- Qty Supply --}}
                                <div class="col-span-2">
                                    <input type="number" name="supplies[{{ $i }}][quantity_supplied]"
                                        value="{{ old('supplies.' . $i . '.quantity_supplied', $supply['quantity_supplied']) }}"
                                        min="0" max="{{ $supply['quantity_requested'] }}"
                                        class="w-full px-2 py-1.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-[8px] text-[13px] text-center font-bold text-brand-600 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all
                                        {{ old('supplies.' . $i . '.quantity_supplied', $supply['quantity_supplied']) < $supply['quantity_requested'] ? 'border-amber-300 bg-amber-50' : '' }}">
                                </div>

                                {{-- Status indicator --}}
                                <div class="col-span-1 flex justify-center">
                                    @php
                                        $supplied = old(
                                            'supplies.' . $i . '.quantity_supplied',
                                            $supply['quantity_supplied'],
                                        );
                                        $requested = $supply['quantity_requested'];
                                    @endphp
                                    @if ($supplied >= $requested)
                                        <span class="text-emerald-500 text-base" title="Terpenuhi">✅</span>
                                    @elseif($supplied > 0)
                                        <span class="text-amber-500 text-base" title="Sebagian">⚠️</span>
                                    @else
                                        <span class="text-rose-400 text-base" title="Tidak terpenuhi">❌</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="grid grid-cols-12 gap-3 pb-3">
                                <div class="col-span-8 col-start-5">
                                    <input type="text" name="supplies[{{ $i }}][notes]"
                                        value="{{ old('supplies.' . $i . '.notes', $supply['notes']) }}"
                                        placeholder="Catatan (opsional, misal: stok terbatas)"
                                        class="w-full px-3 py-1.5 bg-slate-50 border-[1.5px] border-slate-200 rounded-[8px] text-[12px] text-slate-600 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-300">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Summary --}}
                    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between text-[12px]">
                            <span class="text-slate-500">Total request: <strong
                                    class="text-slate-800">{{ $supplies->sum('quantity_requested') }}
                                    pcs</strong></span>
                            <span class="text-slate-500">Total supply: <strong
                                    class="text-brand-600">{{ $supplies->sum('quantity_supplied') }}
                                    pcs</strong></span>
                        </div>
                    </div>

                    <div class="px-5 py-4 border-t border-slate-100 flex items-center gap-3">
                        <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                            style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                            Simpan Supply
                        </button>
                        <a href="{{ route('attendances.show', $attendance) }}"
                            class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                            Batal
                        </a>
                        <a href="{{ route('attendances.invoice', $attendance) }}"
                            class="ml-auto px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
                            🧾 Generate Invoice
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

</x-layouts.app>
