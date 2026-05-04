{{-- resources/views/attendances/show.blade.php --}}
<x-layouts.app title="Detail Kunjungan">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('attendances.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">
                ←
            </a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Detail Kunjungan</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">
                    {{ $attendance->store_name }} ·
                    {{ $attendance->attendance_date->locale('id')->isoFormat('D MMMM Y') }}
                </p>
            </div>
        </div>
        <a href="{{ route('attendances.edit', $attendance) }}"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[10px] transition-colors"
            style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
            ✏️ Edit Kunjungan
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Info Kunjungan --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Info Kunjungan</div>
                </div>
                <div class="divide-y divide-slate-50">

                    {{-- Sales --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Sales</div>
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-lg bg-brand-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                {{ strtoupper(substr($attendance->user->name ?? 'User Telah Dihapus', 0, 2)) }}
                            </div>
                            <span
                                class="text-[13px] font-semibold text-slate-800">{{ $attendance->user->name ?? 'User Telah Dihapus' }}</span>
                        </div>
                    </div>

                    {{-- Toko --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Nama Toko
                        </div>
                        <div class="text-[13px] font-semibold text-slate-800">{{ $attendance->store_name }}</div>
                    </div>

                    {{-- PIC --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">PIC</div>
                        <div class="text-[13px] font-semibold text-slate-800">{{ $attendance->person_in_charge_name }}
                        </div>
                        <div class="text-[12px] text-slate-400 mt-0.5">{{ $attendance->person_in_charge_phone }}</div>
                    </div>

                    {{-- Jenis Pembayaran --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Jenis
                            Pembayaran</div>
                        <div class="text-[13px] font-semibold text-slate-800">
                            {{ $attendance->jenis_pembayaran ?? '—' }}
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Tanggal</div>
                        <div class="text-[13px] font-semibold text-slate-800">
                            {{ $attendance->attendance_date->locale('id')->isoFormat('dddd, D MMMM Y') }}
                        </div>
                    </div>

                    {{-- Check-in --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Check-in
                        </div>
                        <div class="text-[13px] font-semibold text-slate-800">
                            {{ $attendance->checkin_time->format('H:i') }}</div>
                    </div>

                    {{-- Check-out --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Check-out
                        </div>
                        @if ($attendance->checkout_time)
                            <div class="text-[13px] font-semibold text-slate-800">
                                {{ $attendance->checkout_time->format('H:i') }}</div>
                        @else
                            <span class="text-[13px] font-semibold text-slate-800">—</span>
                        @endif
                    </div>

                    {{-- Durasi --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Durasi</div>
                        @if ($attendance->work_duration_minutes && !$attendance->is_auto_checkout)
                            @php
                                $h = intdiv($attendance->work_duration_minutes, 60);
                                $m = $attendance->work_duration_minutes % 60;
                            @endphp
                            <span class="text-[13px] font-semibold text-brand-600">
                                {{ $h > 0 ? $h . ' jam ' : '' }}{{ $m }} menit
                            </span>
                        @elseif(!$attendance->checkout_time)
                            <span class="text-[12px] text-amber-500 font-semibold">Berlangsung</span>
                        @else
                            <span class="text-[13px] text-slate-300">—</span>
                        @endif
                    </div>

                    {{-- Status --}}
                    <div class="px-5 py-3.5">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Status</div>
                        @if ($attendance->is_auto_checkout)
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-red-50 text-red-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Tidak Checkout
                            </span>
                        @elseif($attendance->checkout_time)
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Di Lapangan
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Foto & Lokasi --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Foto & Lokasi</div>
                </div>
                <div class="p-5 space-y-3">
                    {{-- Foto Check-in --}}
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-2">Foto Check-in
                        </div>
                        <img src="{{ Storage::url($attendance->checkin_photo) }}" alt="Foto Check-in"
                            class="w-full rounded-xl object-cover cursor-pointer hover:opacity-90 transition-opacity"
                            style="max-height:180px"
                            onclick="openPhotoModal('Foto Check-in', '{{ $attendance->store_name }} · {{ $attendance->checkin_time->format('H:i') }}', '{{ Storage::url($attendance->checkin_photo) }}')">
                    </div>

                    {{-- Lokasi Check-in --}}
                    <a href="https://www.google.com/maps?q={{ $attendance->checkin_latitude }},{{ $attendance->checkin_longitude }}"
                        target="_blank"
                        class="flex items-center gap-2 px-3 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 rounded-xl transition-colors">
                        <span class="text-base">📍</span>
                        <div>
                            <div class="text-[12px] font-semibold text-rose-600">Lokasi Check-in</div>
                            <div class="text-[11px] text-rose-400 font-mono">{{ $attendance->checkin_latitude }},
                                {{ $attendance->checkin_longitude }}</div>
                        </div>
                        <span class="ml-auto text-[11px] text-rose-400">↗</span>
                    </a>

                    @if ($attendance->checkout_photo)
                        <div class="pt-1">
                            <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-2">Foto
                                Check-out</div>
                            <img src="{{ Storage::url($attendance->checkout_photo) }}" alt="Foto Check-out"
                                class="w-full rounded-xl object-cover cursor-pointer hover:opacity-90 transition-opacity"
                                style="max-height:180px"
                                onclick="openPhotoModal('Foto Check-out', '{{ $attendance->store_name }} · {{ $attendance->checkout_time->format('H:i') }}', '{{ Storage::url($attendance->checkout_photo) }}')">
                        </div>
                    @endif

                    @if ($attendance->checkout_latitude)
                        <a href="https://www.google.com/maps?q={{ $attendance->checkout_latitude }},{{ $attendance->checkout_longitude }}"
                            target="_blank"
                            class="flex items-center gap-2 px-3 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-100 rounded-xl transition-colors">
                            <span class="text-base">📍</span>
                            <div>
                                <div class="text-[12px] font-semibold text-rose-600">Lokasi Check-out</div>
                                <div class="text-[11px] text-rose-400 font-mono">{{ $attendance->checkout_latitude }},
                                    {{ $attendance->checkout_longitude }}</div>
                            </div>
                            <span class="ml-auto text-[11px] text-rose-400">↗</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Daftar Items --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <div class="text-[14px] font-bold text-slate-800 tracking-tight">Daftar Part</div>
                        @if ($attendance->items->count() > 0)
                            <div class="text-[12px] text-slate-400 mt-0.5">
                                {{ $attendance->items->count() }} item · Total qty:
                                {{ $attendance->items->sum('quantity') }}
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('attendances.edit', $attendance) }}#items"
                        class="text-[12px] font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                        Edit Items →
                    </a>
                </div>
                @if ($attendance->items->count() > 0)
                    <table class="w-full">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th
                                    class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-2.5 text-left w-10">
                                    No</th>
                                <th
                                    class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-2.5 text-left">
                                    Nomor Part</th>
                                <th
                                    class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-2.5 text-center w-24">
                                    Qty</th>
                                <th
                                    class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-2.5 text-left">
                                    Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach ($attendance->items as $i => $item)
                                <tr class="hover:bg-brand-50/30 transition-colors">
                                    <td class="px-5 py-3 text-[12px] text-slate-400">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3 text-[13px] font-semibold text-slate-800 font-mono">
                                        {{ $item->part_number }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span
                                            class="text-[13px] font-bold text-brand-600">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-[12px] text-slate-500">{{ $item->notes ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-5 py-10 text-center">
                        <div class="text-2xl mb-2">📦</div>
                        <div class="text-[13px] font-semibold text-slate-500">Belum ada data part</div>
                        <div class="text-[12px] text-slate-400 mt-1">Part akan muncul setelah sales menginput saat
                            checkout</div>
                    </div>
                @endif
            </div>

            {{-- History Log --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">History Perubahan</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">Log setiap perubahan data kunjungan ini</div>
                </div>

                @if ($logs->count() > 0)
                    <div class="px-5 py-4 space-y-4">
                        @foreach ($logs as $log)
                            <div class="flex gap-3">
                                {{-- Avatar --}}
                                <div
                                    class="flex-shrink-0 w-7 h-7 rounded-lg bg-brand-600 flex items-center justify-center text-[10px] font-bold text-white mt-0.5">
                                    {{ strtoupper(substr($log['user']->name, 0, 2)) }}
                                </div>
                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span
                                            class="text-[13px] font-semibold text-slate-800">{{ $log['user']->name }}</span>
                                        <span
                                            class="text-[11px] text-slate-400">{{ $log['created_at']->locale('id')->isoFormat('D MMM Y, HH:mm') }}</span>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($log['changes'] as $change)
                                            @if ($change['is_items'])
                                                {{-- Items change --}}
                                                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3">
                                                    <div class="text-[12px] font-semibold text-slate-600 mb-2">📦
                                                        Daftar part diperbarui</div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <div
                                                                class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">
                                                                Sebelum</div>
                                                            @if (is_array($change['old_value']) && count($change['old_value']) > 0)
                                                                @foreach ($change['old_value'] as $oldItem)
                                                                    <div class="text-[11px] text-slate-500 font-mono">
                                                                        {{ $oldItem['part_number'] }} ×
                                                                        {{ $oldItem['quantity'] }}
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-[11px] text-slate-300">Kosong</div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div
                                                                class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">
                                                                Sesudah</div>
                                                            @if (is_array($change['new_value']) && count($change['new_value']) > 0)
                                                                @foreach ($change['new_value'] as $newItem)
                                                                    <div
                                                                        class="text-[11px] text-slate-800 font-mono font-semibold">
                                                                        {{ $newItem['part_number'] }} ×
                                                                        {{ $newItem['quantity'] }}
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <div class="text-[11px] text-slate-300">Kosong</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Field change --}}
                                                <div class="flex items-start gap-2 text-[12px]">
                                                    <span class="text-slate-400 mt-0.5">•</span>
                                                    <div>
                                                        <span class="font-semibold text-slate-600">
                                                            @php
                                                                $fieldLabels = [
                                                                    'store_name' => 'Nama Toko',
                                                                    'person_in_charge_name' => 'Nama PIC',
                                                                    'person_in_charge_phone' => 'No. Telepon PIC',
                                                                ];
                                                            @endphp
                                                            {{ $fieldLabels[$change['field_name']] ?? $change['field_name'] }}
                                                        </span>
                                                        <span class="text-slate-400"> diubah dari </span>
                                                        <span
                                                            class="line-through text-rose-400">{{ $change['old_value'] }}</span>
                                                        <span class="text-slate-400"> → </span>
                                                        <span
                                                            class="font-semibold text-emerald-600">{{ $change['new_value'] }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if (!$loop->last)
                                <div class="h-px bg-slate-100"></div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="px-5 py-10 text-center">
                        <div class="text-2xl mb-2">📝</div>
                        <div class="text-[13px] font-semibold text-slate-500">Belum ada perubahan</div>
                        <div class="text-[12px] text-slate-400 mt-1">History akan muncul jika ada data yang diedit
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal: Foto --}}
    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7);backdrop-filter:blur(6px)">
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-lg w-full">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                <div>
                    <div id="photo-modal-title" class="text-[14px] font-bold text-slate-800">Foto</div>
                    <div id="photo-modal-sub" class="text-[12px] text-slate-400 mt-0.5"></div>
                </div>
                <button onclick="closePhotoModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors text-sm">
                    ✕
                </button>
            </div>
            <div class="p-4 bg-slate-50 flex items-center justify-center min-h-[260px]">
                <img id="photo-modal-img" src="" alt="Foto"
                    class="max-w-full max-h-[60vh] rounded-xl object-contain shadow-md">
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openPhotoModal(title, sub, src) {
                document.getElementById('photo-modal-title').textContent = title;
                document.getElementById('photo-modal-sub').textContent = sub;
                document.getElementById('photo-modal-img').src = src;
                const modal = document.getElementById('photo-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closePhotoModal() {
                const modal = document.getElementById('photo-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            document.getElementById('photo-modal').addEventListener('click', function(e) {
                if (e.target === this) closePhotoModal();
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') closePhotoModal();
            });
        </script>
    @endpush

</x-layouts.app>
