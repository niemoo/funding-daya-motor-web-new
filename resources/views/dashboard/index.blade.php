<x-layouts.app title="Dashboard">

    {{-- ── Stat Cards ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Kunjungan Hari Ini --}}
        <div
            class="bg-white border border-slate-200 rounded-2xl p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md hover:border-brand-100 transition-all duration-200">
            <div class="absolute top-0 right-0 w-16 h-16 rounded-bl-full bg-brand-600/5"></div>
            <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center text-lg mb-3">📍</div>
            <div class="text-[30px] font-extrabold text-slate-800 tracking-tight leading-none mb-1">{{ $todayVisits }}
            </div>
            <div class="text-[12px] text-slate-400 font-medium">Kunjungan Hari Ini</div>
            <div
                class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                ↑ {{ $todayVisits }} kunjungan hari ini
            </div>
        </div>

        {{-- Sudah Check-out --}}
        <div
            class="bg-white border border-slate-200 rounded-2xl p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-100 transition-all duration-200">
            <div class="absolute top-0 right-0 w-16 h-16 rounded-bl-full bg-emerald-500/5"></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-lg mb-3">✅</div>
            <div class="text-[30px] font-extrabold text-slate-800 tracking-tight leading-none mb-1">{{ $checkedOut }}
            </div>
            <div class="text-[12px] text-slate-400 font-medium">Sudah Check-out</div>
            <div
                class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                ✓ Kunjungan selesai
            </div>
        </div>

        {{-- Masih di Lapangan --}}
        <div
            class="bg-white border border-slate-200 rounded-2xl p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md hover:border-amber-100 transition-all duration-200">
            <div class="absolute top-0 right-0 w-16 h-16 rounded-bl-full bg-amber-500/5"></div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-lg mb-3">⏳</div>
            <div class="text-[30px] font-extrabold text-slate-800 tracking-tight leading-none mb-1">{{ $stillOut }}
            </div>
            <div class="text-[12px] text-slate-400 font-medium">Masih di Lapangan</div>
            <div
                class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">
                ⚡ Sedang aktif
            </div>
        </div>

        {{-- Total Sales / Total Kunjungan Bulan Ini --}}
        <div
            class="bg-white border border-slate-200 rounded-2xl p-5 relative overflow-hidden hover:-translate-y-0.5 hover:shadow-md hover:border-rose-100 transition-all duration-200">
            <div class="absolute top-0 right-0 w-16 h-16 rounded-bl-full bg-rose-500/5"></div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-lg mb-3">👥</div>
            <div class="text-[30px] font-extrabold text-slate-800 tracking-tight leading-none mb-1">{{ $totalSales }}
            </div>
            <div class="text-[12px] text-slate-400 font-medium">
                {{ $isAdmin ? 'Total Sales Aktif' : 'Kunjungan Bulan Ini' }}
            </div>
            <div
                class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-500">
                {{ $isAdmin ? '👤 Terdaftar' : '📅 ' . now()->locale('id')->isoFormat('MMMM Y') }}
            </div>
        </div>
    </div>

    {{-- ── Chart + Activity ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

        {{-- Bar Chart --}}
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Kunjungan 7 Hari Terakhir</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">Total kunjungan per hari</div>
                </div>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">Minggu
                    Ini</span>
            </div>
            <div class="p-5">
                {{-- Chart --}}
                <div class="flex items-end gap-2 h-32">
                    @foreach ($chartData as $day)
                        @php
                            $heightPct = $chartMax > 0 ? round(($day['count'] / $chartMax) * 100) : 0;
                            $isToday = $loop->last;
                        @endphp
                        <div class="flex-1 flex flex-col items-center gap-1.5 h-full">
                            <div class="w-full flex items-end justify-center" style="height: calc(100% - 20px)">
                                <div class="w-full rounded-t-md transition-all duration-500 relative group"
                                    style="height: {{ max($heightPct, 4) }}%; background: {{ $isToday ? 'linear-gradient(to top, #1D61AF, #4a90d9)' : ($heightPct > 0 ? 'linear-gradient(to top, #1D61AF, #4a90d9)' : '#EBF3FC') }}; {{ $isToday ? 'box-shadow: 0 0 12px rgba(29,97,175,0.3)' : '' }}; opacity: {{ $isToday ? '1' : ($heightPct > 0 ? '0.6' : '1') }}">
                                    {{-- Tooltip --}}
                                    @if ($day['count'] > 0)
                                        <div
                                            class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] font-semibold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                            {{ $day['count'] }} kunjungan
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="text-[10px] font-medium {{ $isToday ? 'text-brand-600 font-bold' : 'text-slate-400' }}">
                                {{ $day['label'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <div class="text-[14px] font-bold text-slate-800 tracking-tight">Aktivitas Terbaru</div>
            </div>
            <div class="px-4 py-2 divide-y divide-slate-50">
                @forelse($recentActivity as $act)
                    @php
                        $isCheckout = !is_null($act->checkout_time);
                        $time = $isCheckout ? $act->checkout_time : $act->checkin_time;
                    @endphp
                    <div class="flex gap-3 py-2.5">
                        <div
                            class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $isCheckout ? 'bg-brand-600' : 'bg-emerald-500' }}">
                        </div>
                        <div class="min-w-0">
                            <div class="text-[13px] text-slate-600 leading-snug">
                                <span class="font-semibold text-slate-800">{{ $act->user->name }}</span>
                                {{ $isCheckout ? 'check-out dari' : 'check-in di' }}
                                <span class="font-medium">{{ $act->store_name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $time->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-[13px] text-slate-400">
                        Belum ada aktivitas hari ini
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Bottom Section ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Top Sales (admin) / Kunjungan Hari Ini (sales) --}}
        @if ($isAdmin)
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Top Sales Bulan Ini</div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600">
                        {{ now()->locale('id')->isoFormat('MMMM Y') }}
                    </span>
                </div>
                <div class="p-5 space-y-4">
                    @forelse($topSales as $index => $sales)
                        @php
                            $pct = $maxVisits > 0 ? round(($sales->month_visits / $maxVisits) * 100) : 0;
                            $medals = ['🥇', '🥈', '🥉'];
                            $colors = [
                                'linear-gradient(90deg,#1D61AF,#4a90d9)',
                                'linear-gradient(90deg,#0891b2,#38bdf8)',
                                'linear-gradient(90deg,#059669,#34d399)',
                                'linear-gradient(90deg,#d97706,#fbbf24)',
                                'linear-gradient(90deg,#9333ea,#c084fc)',
                            ];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-[13px]">{{ $medals[$index] ?? '🏅' }}</span>
                                    <span class="text-[13px] font-semibold text-slate-700">{{ $sales->name }}</span>
                                </div>
                                <span class="text-[12px] text-slate-400">{{ $sales->month_visits }} kunjungan</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700"
                                    style="width: {{ $pct }}%; background: {{ $colors[$index] ?? $colors[4] }}">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-[13px] text-slate-400">
                            Belum ada data kunjungan bulan ini
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Kunjungan Hari Ini --}}
        <div
            class="bg-white border border-slate-200 rounded-2xl overflow-hidden {{ !$isAdmin ? 'lg:col-span-2' : '' }}">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div class="text-[14px] font-bold text-slate-800 tracking-tight">Kunjungan Hari Ini</div>
                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full bg-brand-50 text-brand-600">
                    {{ now()->locale('id')->isoFormat('D MMM') }}
                </span>
            </div>
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        @if ($isAdmin)
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                                Sales</th>
                        @endif
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                            Toko</th>
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                            Check-in</th>
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentAttendances as $att)
                        <tr class="hover:bg-brand-50/40 transition-colors">
                            @if ($isAdmin)
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-brand-600 flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0">
                                            {{ strtoupper(substr($att->user->name, 0, 2)) }}
                                        </div>
                                        <span
                                            class="text-[13px] font-semibold text-slate-800">{{ $att->user->name }}</span>
                                    </div>
                                </td>
                            @endif
                            <td class="px-4 py-3 text-[13px] text-slate-600 max-w-[140px] truncate">
                                {{ $att->store_name }}</td>
                            <td class="px-4 py-3 text-[13px] font-semibold text-slate-800">
                                {{ $att->checkin_time->format('H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($att->checkout_time)
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>Di
                                        Lapangan
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-[13px] text-slate-400">
                                Belum ada kunjungan hari ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($recentAttendances->count() > 0)
                <div class="px-5 py-3 border-t border-slate-100 text-right">
                    <a href="{{ route('attendances.index') }}"
                        class="text-[12px] font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                        Lihat semua →
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-layouts.app>
