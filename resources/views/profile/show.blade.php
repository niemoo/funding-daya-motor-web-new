<x-layouts.app title="Profile">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 rounded-2xl bg-brand-600 flex items-center justify-center text-2xl font-extrabold text-white flex-shrink-0"
            style="box-shadow:0 4px 16px rgba(29,97,175,0.25)">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div>
            <h2 class="text-[22px] font-extrabold text-slate-800 tracking-tight leading-none">{{ $user->name }}</h2>
            <div class="flex items-center gap-2 mt-1.5">
                <span
                    class="inline-flex items-center gap-1 text-[12px] font-semibold px-2.5 py-1 rounded-full
                    {{ $user->role->name === 'Admin' ? 'bg-brand-50 text-brand-600' : 'bg-emerald-50 text-emerald-600' }}">
                    {{ $user->role->name === 'Admin' ? '🛡️' : '🧑‍💼' }} {{ $user->role->name }}
                </span>
                <span class="text-[12px] text-slate-400">Bergabung
                    {{ $user->created_at->locale('id')->isoFormat('D MMMM Y') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Kolom Kiri: Info + Stats --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Info Card --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Informasi Akun</div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Nama Lengkap
                        </div>
                        <div class="text-[14px] font-semibold text-slate-800">{{ $user->name }}</div>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Email</div>
                        <div class="text-[14px] font-semibold text-slate-800">{{ $user->email }}</div>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Role</div>
                        <div class="text-[14px] font-semibold text-slate-800">{{ $user->role->name }}</div>
                    </div>
                    <div class="h-px bg-slate-100"></div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 mb-1">Employee ID
                        </div>
                        <div class="text-[14px] font-semibold text-slate-800 font-mono">
                            #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Statistik Kunjungan</div>
                </div>
                <div class="divide-y divide-slate-50">
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-[8px] bg-brand-50 flex items-center justify-center text-sm">📍
                            </div>
                            <span class="text-[13px] text-slate-600">Total Kunjungan</span>
                        </div>
                        <span class="text-[15px] font-extrabold text-slate-800">{{ $totalVisits }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-[8px] bg-emerald-50 flex items-center justify-center text-sm">✅
                            </div>
                            <span class="text-[13px] text-slate-600">Selesai</span>
                        </div>
                        <span class="text-[15px] font-extrabold text-slate-800">{{ $completedVisits }}</span>
                    </div>
                    <div class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-[8px] bg-amber-50 flex items-center justify-center text-sm">📅
                            </div>
                            <span class="text-[13px] text-slate-600">Bulan Ini</span>
                        </div>
                        <span class="text-[15px] font-extrabold text-slate-800">{{ $thisMonthVisits }}</span>
                    </div>
                    @if ($autoCheckouts > 0)
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-[8px] bg-red-50 flex items-center justify-center text-sm">⚠️
                                </div>
                                <span class="text-[13px] text-slate-600">Tidak Checkout</span>
                            </div>
                            <span class="text-[15px] font-extrabold text-red-500">{{ $autoCheckouts }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Kunjungan Terakhir + Ganti Password --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Kunjungan Terakhir --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Kunjungan Terakhir</div>
                    <a href="{{ route('attendances.index') }}"
                        class="text-[12px] font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                        Lihat semua →
                    </a>
                </div>
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                                Toko</th>
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                                Tanggal</th>
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                                Check-in</th>
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-left">
                                Durasi</th>
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-2.5 text-center">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentVisits as $att)
                            <tr class="hover:bg-brand-50/40 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="text-[13px] font-semibold text-slate-800">{{ $att->store_name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $att->person_in_charge_name }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-[13px] text-slate-600">
                                        {{ $att->attendance_date->format('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-400">
                                        {{ $att->attendance_date->locale('id')->isoFormat('dddd') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-[13px] font-semibold text-slate-800">
                                        {{ $att->checkin_time->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($att->work_duration_minutes && !$att->is_auto_checkout)
                                        @php
                                            $h = intdiv($att->work_duration_minutes, 60);
                                            $m = $att->work_duration_minutes % 60;
                                        @endphp
                                        <span class="text-[12px] font-semibold text-brand-600">
                                            {{ $h > 0 ? $h . 'j ' : '' }}{{ $m }}m
                                        </span>
                                    @elseif(!$att->checkout_time)
                                        <span class="text-[12px] text-amber-500 font-medium">Berlangsung</span>
                                    @else
                                        <span class="text-[12px] text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">
                                    @if ($att->is_auto_checkout)
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-red-50 text-red-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Tidak Checkout
                                        </span>
                                    @elseif($att->checkout_time)
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Selesai
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>Di
                                            Lapangan
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-[13px] text-slate-400">
                                    Belum ada kunjungan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Ganti Password --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="text-[14px] font-bold text-slate-800 tracking-tight">Ganti Password</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">Pastikan password baru minimal 8 karakter</div>
                </div>
                <form method="POST" action="{{ route('profile.password') }}" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Password Saat Ini</label>
                        <input type="password" name="current_password"
                            class="w-full px-3.5 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all @error('current_password') border-red-400 @enderror"
                            placeholder="••••••••">
                        @error('current_password')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password"
                            class="w-full px-3.5 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all @error('password') border-red-400 @enderror"
                            placeholder="••••••••">
                        @error('password')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Konfirmasi Password
                            Baru</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-3.5 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all"
                            placeholder="••••••••">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                            style="box-shadow:0 3px 10px rgba(29,97,175,0.25)">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.app>
