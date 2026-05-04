<x-layouts.app title="Master Part">

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mx-auto mb-4">🗑️
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 text-center mb-1">Hapus Part</h3>
            <p class="text-[13px] text-slate-400 text-center mb-1">Apakah kamu yakin ingin menghapus</p>
            <p id="modal-part-name" class="text-[14px] font-bold text-slate-700 text-center mb-4">—</p>
            <p class="text-[12px] text-rose-400 text-center mb-5 bg-rose-50 rounded-xl py-2 px-3">
                ⚠️ Tindakan ini tidak dapat dibatalkan
            </p>
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()"
                    class="flex-1 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                    Batal
                </button>
                <form id="delete-form" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full py-2.5 bg-rose-500 hover:bg-rose-600 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Page Header --}}
    <div class="flex items-start justify-between mb-5 gap-4">
        <div>
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Master Part</h1>
            <p class="text-[13px] text-slate-400 mt-1">Total {{ $parts->total() }} part terdaftar</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('parts.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[10px] transition-all"
                style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                ＋ Tambah Part
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('parts.index') }}" class="flex flex-wrap gap-2.5 mb-4 items-center">
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif

        <div class="relative flex-1 min-w-[200px] max-w-[280px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari kode, deskripsi, group..."
                class="w-full pl-8 pr-3 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
        </div>

        <select name="group_id"
            class="py-2.5 px-3 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-600 outline-none focus:border-brand-600 transition-all">
            <option value="">Semua Group</option>
            @foreach ($groups as $group)
                <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
            @endforeach
        </select>

        <button type="submit"
            class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
            Filter
        </button>

        @if (request()->anyFilled(['search', 'group']))
            <a href="{{ route('parts.index') }}"
                class="px-4 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                Reset
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <x-sort-th column="kode_part" label="Kode Part" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="deskripsi_part" label="Deskripsi Part" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="group" label="Group" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="created_at" label="Ditambahkan" :currentSort="$sort" :currentDir="$dir" />
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($parts as $part)
                        <tr class="hover:bg-brand-50/40 transition-colors"
                            data-part-name="{{ addslashes($part->kode_part . ' — ' . $part->deskripsi_part) }}"
                            data-edit-url="{{ route('parts.edit', $part) }}"
                            data-delete-url="{{ route('parts.destroy', $part) }}">

                            {{-- Kode Part --}}
                            <td class="px-4 py-3">
                                <span class="text-[13px] font-semibold text-slate-800 font-mono">
                                    {{ $part->kode_part }}
                                </span>
                            </td>

                            {{-- Deskripsi --}}
                            <td class="px-4 py-3 text-[13px] text-slate-600">
                                {{ $part->deskripsi_part }}
                            </td>

                            {{-- Group --}}
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-brand-50 text-brand-600">
                                    {{ $part->group?->name ?? '—' }}
                                </span>
                            </td>

                            {{-- Ditambahkan --}}
                            <td class="px-4 py-3 text-[13px] text-slate-400">
                                {{ $part->created_at->format('d M Y') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-center">
                                <button onclick="toggleActionMenu(event, 'menu-{{ $part->id }}')"
                                    class="w-8 h-8 flex items-center justify-center mx-auto rounded-[7px] text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors text-xl font-bold">
                                    ⋮
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="text-2xl mb-2">🔧</div>
                                <div class="text-[14px] font-semibold text-slate-500">Tidak ada part ditemukan</div>
                                <div class="text-[12px] text-slate-400 mt-1">Coba ubah filter atau tambah part baru
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($parts->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="text-[12px] text-slate-400">
                    Menampilkan {{ $parts->firstItem() }}–{{ $parts->lastItem() }} dari {{ $parts->total() }} part
                </div>
                <div class="flex items-center gap-1.5">
                    @if ($parts->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">‹</span>
                    @else
                        <a href="{{ $parts->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">‹</a>
                    @endif

                    @foreach ($parts->getUrlRange(1, $parts->lastPage()) as $page => $url)
                        @if ($page == $parts->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-brand-600 text-white text-[13px] font-semibold">{{ $page }}</span>
                        @elseif(abs($page - $parts->currentPage()) <= 2)
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($parts->hasMorePages())
                        <a href="{{ $parts->nextPageUrl() }}"
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
            let currentMenu = null;

            function toggleActionMenu(event, menuId) {
                event.stopPropagation();
                if (currentMenu === menuId) {
                    closeAllMenus();
                    return;
                }
                closeAllMenus();

                const btn = event.currentTarget;
                const rect = btn.getBoundingClientRect();
                const row = btn.closest('tr');
                const editUrl = row.dataset.editUrl;
                const deleteUrl = row.dataset.deleteUrl;
                const partName = row.dataset.partName;

                const menu = document.createElement('div');
                menu.id = 'floating-' + menuId;
                menu.className = 'fixed z-[9999] bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden';
                menu.style.cssText = `min-width:144px; top:${rect.bottom + 4}px; left:${rect.right - 144}px;`;
                menu.innerHTML = `
                <a href="${editUrl}"
                    class="flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors font-medium">
                    ✏️ Edit
                </a>
                <div class="h-px bg-slate-100"></div>
                <button onclick="openDeleteModal('${deleteUrl}', decodeURIComponent('${encodeURIComponent(partName)}'))"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-rose-500 hover:bg-rose-50 transition-colors font-medium">
                    🗑️ Hapus
                </button>
            `;
                document.body.appendChild(menu);
                currentMenu = menuId;

                requestAnimationFrame(() => {
                    const mRect = menu.getBoundingClientRect();
                    if (mRect.bottom > window.innerHeight) menu.style.top = (rect.top - mRect.height - 4) + 'px';
                    if (mRect.left < 0) menu.style.left = rect.left + 'px';
                });
            }

            function closeAllMenus() {
                document.querySelectorAll('[id^="floating-menu-"]').forEach(el => el.remove());
                currentMenu = null;
            }

            document.addEventListener('click', closeAllMenus);
            window.addEventListener('scroll', closeAllMenus, true);
            window.addEventListener('resize', closeAllMenus);

            function openDeleteModal(url, name) {
                document.getElementById('modal-part-name').textContent = name;
                document.getElementById('delete-form').action = url;
                closeAllMenus();
                const modal = document.getElementById('delete-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('delete-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('delete-modal').addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });
        </script>
    @endpush

</x-layouts.app>
