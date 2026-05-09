<x-layouts.app title="Roles">

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mx-auto mb-4">🗑️
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 text-center mb-1">Hapus Role</h3>
            <p class="text-[13px] text-slate-400 text-center mb-1">Apakah kamu yakin ingin menghapus</p>
            <p id="modal-role-name" class="text-[14px] font-bold text-slate-700 text-center mb-4">—</p>
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
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Manajemen Role</h1>
            <p class="text-[13px] text-slate-400 mt-1">Total {{ $roles->count() }} role terdaftar</p>
        </div>
        @can('roles.create')
            <a href="{{ route('roles.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[10px] transition-all flex-shrink-0"
                style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                ＋ Tambah Role
            </a>
        @endcan
    </div>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr>
                    <th
                        class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 text-left bg-slate-50 border-b border-slate-100">
                        Nama Role</th>
                    <th
                        class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 text-left bg-slate-50 border-b border-slate-100">
                        Permissions</th>
                    <th
                        class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 text-center bg-slate-50 border-b border-slate-100">
                        Jumlah User</th>
                    @if (auth()->user()->can('roles.edit') || auth()->user()->can('roles.delete'))
                        <th
                            class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 text-center bg-slate-50 border-b border-slate-100">
                            Aksi
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($roles as $role)
                    <tr class="hover:bg-brand-50/40 transition-colors" data-role-name="{{ addslashes($role->name) }}"
                        data-edit-url="{{ route('roles.edit', $role) }}"
                        data-delete-url="{{ route('roles.destroy', $role) }}"
                        data-users-count="{{ $role->users_count }}">

                        <td class="px-5 py-3.5">
                            <span class="text-[13px] font-semibold text-slate-800">{{ $role->name }}</span>
                        </td>

                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @forelse($role->permissions->take(5) as $permission)
                                    <span
                                        class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">
                                        {{ $permission->name }}
                                    </span>
                                @empty
                                    <span class="text-[12px] text-slate-300">Tidak ada permission</span>
                                @endforelse
                                @if ($role->permissions->count() > 5)
                                    <span
                                        class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                                        +{{ $role->permissions->count() - 5 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="px-5 py-3.5 text-center">
                            <span class="text-[13px] font-semibold text-slate-800">{{ $role->users_count }}</span>
                        </td>

                        @if (auth()->user()->can('roles.edit') || auth()->user()->can('roles.delete'))
                            <td class="px-5 py-3.5 text-center">
                                <button onclick="toggleActionMenu(event, 'menu-{{ $role->id }}')"
                                    class="w-8 h-8 flex items-center justify-center mx-auto rounded-[7px] text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors text-xl font-bold">
                                    ⋮
                                </button>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->can('roles.edit') || auth()->user()->can('roles.delete') ? 4 : 3 }}"
                            class="px-5 py-12 text-center">
                            <div class="text-2xl mb-2">🔐</div>
                            <div class="text-[14px] font-semibold text-slate-500">Tidak ada data role ditemukan</div>
                            <div class="text-[12px] text-slate-400 mt-1">Coba ubah kata kunci pencarian atau tambah
                                data role baru</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
                const roleName = row.dataset.roleName;
                const editUrl = row.dataset.editUrl;
                const deleteUrl = row.dataset.deleteUrl;
                const usersCount = parseInt(row.dataset.usersCount);

                const menu = document.createElement('div');
                menu.id = 'floating-' + menuId;
                menu.className = 'fixed z-[9999] bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden';
                menu.style.cssText = `min-width:144px; top:${rect.bottom + 4}px; left:${rect.right - 144}px;`;
                menu.innerHTML = `
                @can('roles.edit')
                <a href="${editUrl}"
                    class="flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors font-medium">
                    ✏️ Edit & Permissions
                </a>
                @endcan
                @can('roles.delete')
                <div class="h-px bg-slate-100"></div>
                <button onclick="openDeleteModal('${deleteUrl}', decodeURIComponent('${encodeURIComponent(roleName)}'), ${usersCount})"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-rose-500 hover:bg-rose-50 transition-colors font-medium">
                    🗑️ Hapus
                </button>
                @endcan
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

            function openDeleteModal(url, name, usersCount) {
                if (usersCount > 0) {
                    alert(`Role "${name}" tidak bisa dihapus karena masih digunakan oleh ${usersCount} user.`);
                    closeAllMenus();
                    return;
                }
                document.getElementById('modal-role-name').textContent = name;
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
