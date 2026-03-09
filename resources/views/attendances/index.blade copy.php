<x-layouts.app title="Absensi">

    {{-- Modal: Foto --}}
    <div id="photo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7);backdrop-filter:blur(6px)">
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-lg w-full">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                <div>
                    <div id="photo-modal-title" class="text-[14px] font-bold text-slate-800">Foto</div>
                    <div id="photo-modal-sub" class="text-[12px] text-slate-400 mt-0.5"></div>
                </div>
                <button onclick="closeModal('photo-modal')"
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

    {{-- Modal: Maps --}}
    <div id="maps-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background:rgba(0,0,0,0.7);backdrop-filter:blur(6px)">
        <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-2xl w-full">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                <div>
                    <div id="maps-modal-title" class="text-[14px] font-bold text-slate-800">Lokasi</div>
                    <div id="maps-modal-sub" class="text-[12px] text-slate-400 mt-0.5"></div>
                </div>
                <button onclick="closeModal('maps-modal')"
                    class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors text-sm">
                    ✕
                </button>
            </div>
            <div class="p-0">
                <iframe id="maps-iframe" src="" width="100%" height="400" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                <div id="maps-coords" class="text-[12px] text-slate-400 font-mono"></div>
                <a id="maps-external-link" href="#" target="_blank"
                    class="text-[12px] font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                    Buka di Google Maps →
                </a>
            </div>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5 gap-4">
        <div>
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Data Absensi</h1>
            <p class="text-[13px] text-slate-400 mt-1">Total {{ $attendances->total() }} kunjungan tercatat</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            {{-- Export akan ditambahkan nanti --}}
            <button disabled
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-400 text-[13px] font-semibold rounded-[10px] cursor-not-allowed">
                📥 Export Excel
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-wrap gap-2.5 mb-4 items-center">
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px] max-w-[260px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sales, toko, PIC..."
                class="w-full pl-8 pr-3 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
        </div>

        {{-- Filter Sales (admin only) --}}
        @if (auth()->user()->isAdmin())
            <select name="user_id"
                class="py-2.5 px-3 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-600 outline-none focus:border-brand-600 transition-all">
                <option value="">Semua Sales</option>
                @foreach ($salesList as $sales)
                    <option value="{{ $sales->id }}" {{ request('user_id') == $sales->id ? 'selected' : '' }}>
                        {{ $sales->name }}
                    </option>
                @endforeach
            </select>
        @endif

        {{-- Filter Date --}}
        <input type="date" name="date" value="{{ request('date') }}"
            class="py-2.5 px-3 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-600 outline-none focus:border-brand-600 transition-all">

        {{-- Filter Status --}}
        <select name="status"
            class="py-2.5 px-3 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-600 outline-none focus:border-brand-600 transition-all">
            <option value="">Semua Status</option>
            <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Di Lapangan</option>
            <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Selesai</option>
        </select>

        <button type="submit"
            class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
            Filter
        </button>

        @if (request()->anyFilled(['search', 'user_id', 'date', 'status']))
            <a href="{{ route('attendances.index') }}"
                class="px-4 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="min-width: 1100px">
                <thead>
                    <tr>
                        @if (auth()->user()->isAdmin())
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-left bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                                Sales</th>
                        @endif
                        <x-sort-th column="store_name" label="Toko & PIC" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="attendance_date" label="Tanggal" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="checkin_time" label="Check-in" :currentSort="$sort" :currentDir="$dir" />
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                            Foto In</th>
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-left bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                            Check-out</th>
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                            Foto Out</th>
                        <x-sort-th column="work_duration_minutes" label="Durasi" :currentSort="$sort" :currentDir="$dir" />
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                            Lokasi</th>
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100 whitespace-nowrap">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($attendances as $att)
                        <tr class="hover:bg-brand-50/40 transition-colors">

                            {{-- Sales (admin only) --}}
                            @if (auth()->user()->isAdmin())
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-brand-600 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0">
                                            {{ strtoupper(substr($att->user->name, 0, 2)) }}
                                        </div>
                                        <span
                                            class="text-[13px] font-semibold text-slate-800">{{ $att->user->name }}</span>
                                    </div>
                                </td>
                            @endif

                            {{-- Toko & PIC --}}
                            <td class="px-4 py-3" style="min-width:180px">
                                <div class="text-[13px] font-semibold text-slate-800 leading-snug">
                                    {{ $att->store_name }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $att->person_in_charge_name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $att->person_in_charge_phone }}</div>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-[13px] font-semibold text-slate-800">
                                    {{ $att->attendance_date->format('d M Y') }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $att->attendance_date->locale('id')->isoFormat('dddd') }}
                                </div>
                            </td>

                            {{-- Check-in --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-[13px] font-bold text-slate-800">
                                    {{ $att->checkin_time->format('H:i') }}
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono">
                                    {{ $att->checkin_time->format('d/m/Y') }}
                                </div>
                            </td>

                            {{-- Foto Check-in --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button
                                    onclick="openPhotoModal(
                                    '{{ addslashes($att->store_name) }}',
                                    'Foto Check-in · {{ $att->checkin_time->format('H:i, d M Y') }}',
                                    '{{ asset($att->checkin_photo) }}'
                                )"
                                    class="w-8 h-8 flex items-center justify-center mx-auto rounded-[8px] bg-brand-50 hover:bg-brand-100 text-brand-600 transition-colors text-base"
                                    title="Lihat foto check-in">
                                    🖼️
                                </button>
                            </td>

                            {{-- Check-out --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($att->checkout_time)
                                    <div class="text-[13px] font-bold text-slate-800">
                                        {{ $att->checkout_time->format('H:i') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono">
                                        {{ $att->checkout_time->format('d/m/Y') }}
                                    </div>
                                @else
                                    <span class="text-slate-300 text-[13px]">—</span>
                                @endif
                            </td>

                            {{-- Foto Check-out --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($att->checkout_time && $att->checkout_photo)
                                    <button
                                        onclick="openPhotoModal(
                                    '{{ addslashes($att->store_name) }}',
                                    'Foto Check-out · {{ $att->checkout_time->format('H:i, d M Y') }}',
                                    '{{ asset($att->checkout_photo) }}'
                                )"
                                        class="w-8 h-8 flex items-center justify-center mx-auto rounded-[8px] bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-colors text-base"
                                        title="Lihat foto check-out">
                                        🖼️
                                    </button>
                                @else
                                    <span class="text-slate-200 text-lg">🖼️</span>
                                @endif
                            </td>

                            {{-- Durasi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($att->work_duration_minutes)
                                    @php
                                        $hours = intdiv($att->work_duration_minutes, 60);
                                        $minutes = $att->work_duration_minutes % 60;
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 text-[12px] font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">
                                        ⏱
                                        @if ($hours > 0)
                                            {{ $hours }}j
                                        @endif{{ $minutes }}m
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-[12px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-500">
                                        ⏳ Berlangsung
                                    </span>
                                @endif
                            </td>

                            {{-- Lokasi --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <button
                                    onclick="openMapsModal(
                                    '{{ addslashes($att->store_name) }}',
                                    'Check-in · {{ $att->checkin_time->format('H:i, d M Y') }}',
                                    '{{ $att->checkin_latitude }}',
                                    '{{ $att->checkin_longitude }}'
                                )"
                                    class="w-8 h-8 flex items-center justify-center mx-auto rounded-[8px] bg-rose-50 hover:bg-rose-100 text-rose-500 transition-colors text-base"
                                    title="Lihat lokasi">
                                    📍
                                </button>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($att->checkout_time)
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Di
                                        Lapangan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 10 : 9 }}" class="px-4 py-12 text-center">
                                <div class="text-2xl mb-2">📋</div>
                                <div class="text-[14px] font-semibold text-slate-500">Tidak ada data absensi</div>
                                {{-- <div class="text-[12px] text-slate-400 mt-1">Coba ubah filter pencarian</div> --}}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($attendances->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="text-[12px] text-slate-400">
                    Menampilkan {{ $attendances->firstItem() }}–{{ $attendances->lastItem() }} dari
                    {{ $attendances->total() }} data
                </div>
                <div class="flex items-center gap-1.5">
                    @if ($attendances->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">‹</span>
                    @else
                        <a href="{{ $attendances->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">‹</a>
                    @endif

                    @foreach ($attendances->getUrlRange(1, $attendances->lastPage()) as $page => $url)
                        @if ($page == $attendances->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-brand-600 text-white text-[13px] font-semibold">{{ $page }}</span>
                        @elseif(abs($page - $attendances->currentPage()) <= 2)
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($attendances->hasMorePages())
                        <a href="{{ $attendances->nextPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">›</a>
                    @else
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // ── Photo Modal ──
            function openPhotoModal(title, sub, imgSrc) {
                document.getElementById('photo-modal-title').textContent = title;
                document.getElementById('photo-modal-sub').textContent = sub;
                document.getElementById('photo-modal-img').src = imgSrc;
                openModal('photo-modal');
            }

            // ── Maps Modal ──
            function openMapsModal(title, sub, lat, lng) {
                document.getElementById('maps-modal-title').textContent = title;
                document.getElementById('maps-modal-sub').textContent = sub;
                document.getElementById('maps-coords').textContent = lat + ', ' + lng;

                const embedUrl = `https://www.google.com/maps?q=${lat},${lng}&hl=id&z=17&output=embed`;
                const externalUrl = `https://www.google.com/maps?q=${lat},${lng}`;
                document.getElementById('maps-iframe').src = embedUrl;
                document.getElementById('maps-external-link').href = externalUrl;
                openModal('maps-modal');
            }

            // ── Modal helpers ──
            function openModal(id) {
                const modal = document.getElementById(id);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeModal(id) {
                const modal = document.getElementById(id);
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
                // Reset iframe supaya maps tidak terus loading di background
                if (id === 'maps-modal') {
                    document.getElementById('maps-iframe').src = '';
                }
            }

            // Close on backdrop click
            ['photo-modal', 'maps-modal'].forEach(id => {
                document.getElementById(id).addEventListener('click', function(e) {
                    if (e.target === this) closeModal(id);
                });
            });

            // Close on ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal('photo-modal');
                    closeModal('maps-modal');
                }
            });
        </script>
    @endpush

</x-layouts.app>
