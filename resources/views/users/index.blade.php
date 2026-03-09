<x-layouts.app title="Users">

    {{-- Delete Confirmation Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mx-auto mb-4">🗑️
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 text-center mb-1">Hapus User</h3>
            <p class="text-[13px] text-slate-400 text-center mb-1">Apakah kamu yakin ingin menghapus</p>
            <p id="modal-user-name" class="text-[14px] font-bold text-slate-700 text-center mb-4">—</p>
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
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Daftar Users</h1>
            <p class="text-[13px] text-slate-400 mt-1">Total {{ $users->total() }} pengguna terdaftar di sistem</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <a href="{{ route('users.export', request()->query()) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-semibold rounded-[10px] transition-all"
                style="box-shadow: 0 3px 10px rgba(5,150,105,0.25)">
                📥 Export Excel
            </a>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[10px] transition-all"
                style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                ＋ Tambah User
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-2.5 mb-4 items-center">
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
        <div class="relative flex-1 min-w-[200px] max-w-[280px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                class="w-full pl-8 pr-3 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
        </div>
        <select name="role"
            class="py-2.5 px-3 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-600 outline-none focus:border-brand-600 transition-all">
            <option value="">Semua Role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                    {{ $role->name }}</option>
            @endforeach
        </select>
        <button type="submit"
            class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
            Filter
        </button>
        @if (request()->anyFilled(['search', 'role']))
            <a href="{{ route('users.index') }}"
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
                        <x-sort-th column="name" label="User" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="email" label="Email" :currentSort="$sort" :currentDir="$dir" />
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-left bg-slate-50 border-b border-slate-100">
                            Role</th>
                        <x-sort-th column="created_at" label="Bergabung" :currentSort="$sort" :currentDir="$dir" />
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-brand-50/40 transition-colors"
                            data-user-name="{{ addslashes($user->name) }}"
                            data-edit-url="{{ route('users.edit', $user) }}"
                            data-is-self="{{ $user->id === auth()->id() ? 'true' : 'false' }}">

                            {{-- User --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="w-8 h-8 rounded-[9px] bg-brand-600 flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-semibold text-slate-800">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400">
                                            #{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-[13px] text-slate-500">{{ $user->email }}</td>

                            {{-- Role --}}
                            <td class="px-4 py-3">
                                @if ($user->role->name === 'Admin')
                                    <span
                                        class="inline-flex items-center text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-brand-50 text-brand-600">Admin</span>
                                @else
                                    <span
                                        class="inline-flex items-center text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-600">Sales</span>
                                @endif
                            </td>

                            {{-- Bergabung --}}
                            <td class="px-4 py-3 text-[13px] text-slate-400">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 text-center">
                                <button onclick="toggleActionMenu(event, 'menu-{{ $user->id }}')"
                                    class="w-8 h-8 flex items-center justify-center mx-auto rounded-[7px] text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors text-xl font-bold">
                                    ⋮
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="text-2xl mb-2">👥</div>
                                <div class="text-[14px] font-semibold text-slate-500">Tidak ada user ditemukan</div>
                                <div class="text-[12px] text-slate-400 mt-1">Coba ubah filter pencarian</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="text-[12px] text-slate-400">
                    Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} users
                </div>
                <div class="flex items-center gap-1.5">
                    @if ($users->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">‹</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">‹</a>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-brand-600 text-white text-[13px] font-semibold">{{ $page }}</span>
                        @elseif(abs($page - $users->currentPage()) <= 2)
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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
                const userId = menuId.replace('menu-', '');
                const userName = row.dataset.userName;
                const editUrl = row.dataset.editUrl;
                const isSelf = row.dataset.isSelf === 'true';

                const menu = document.createElement('div');
                menu.id = 'floating-' + menuId;
                menu.className = 'fixed z-[9999] bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden';
                menu.style.cssText = `min-width:144px; top:${rect.bottom + 4}px; left:${rect.right - 144}px;`;

                menu.innerHTML = `
        <a href="${editUrl}"
           class="flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-slate-600 hover:bg-blue-50 hover:text-blue-600 transition-colors font-medium">
            ✏️ Edit
        </a>
        ${!isSelf ? `
                <div class="h-px bg-slate-100 mx-0"></div>
                <button onclick="openDeleteModal('${userId}', decodeURIComponent('${encodeURIComponent(userName)}'))"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-rose-500 hover:bg-rose-50 transition-colors font-medium">
                    🗑️ Hapus
                </button>` : ''}
    `;

                document.body.appendChild(menu);
                currentMenu = menuId;

                // Cek apakah menu keluar viewport bawah
                requestAnimationFrame(() => {
                    const mRect = menu.getBoundingClientRect();
                    if (mRect.bottom > window.innerHeight) {
                        menu.style.top = (rect.top - mRect.height - 4) + 'px';
                    }
                    if (mRect.left < 0) {
                        menu.style.left = (rect.left) + 'px';
                    }
                });
            }

            function closeAllMenus() {
                document.querySelectorAll('[id^="floating-menu-"]').forEach(el => el.remove());
                currentMenu = null;
            }

            document.addEventListener('click', closeAllMenus);
            window.addEventListener('scroll', closeAllMenus, true);
            window.addEventListener('resize', closeAllMenus);

            function openDeleteModal(userId, userName) {
                document.getElementById('modal-user-name').textContent = userName;
                document.getElementById('delete-form').action = '/users/' + userId;
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
