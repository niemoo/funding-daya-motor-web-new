<x-layouts.app title="Toko Umum">

    {{-- Delete Modal --}}
    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background: rgba(0,0,0,0.4); backdrop-filter: blur(4px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl mx-auto mb-4">🗑️
            </div>
            <h3 class="text-[16px] font-bold text-slate-800 text-center mb-1">Hapus Toko</h3>
            <p class="text-[13px] text-slate-400 text-center mb-1">Apakah kamu yakin ingin menghapus</p>
            <p id="modal-store-name" class="text-[14px] font-bold text-slate-700 text-center mb-4">—</p>
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
            <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Toko Umum</h1>
            <p class="text-[13px] text-slate-400 mt-1">Total {{ $stores->total() }} toko terdaftar</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            @can('general-stores.import')
                <button onclick="openImportModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border-[1.5px] border-slate-200 hover:bg-slate-50 text-slate-600 text-[13px] font-semibold rounded-[10px] transition-all">
                    📂 Import Excel
                </button>
            @endcan
            @can('general-stores.create')
                <a href="{{ route('general-stores.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[10px] transition-all"
                    style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                    ＋ Tambah Toko
                </a>
            @endcan
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('general-stores.index') }}" class="flex flex-wrap gap-2.5 mb-4 items-center">
        @if (request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="dir" value="{{ request('dir') }}">
        @endif
        <div class="relative flex-1 min-w-[200px] max-w-[280px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama toko..."
                class="w-full pl-8 pr-3 py-2.5 bg-white border-[1.5px] border-slate-200 rounded-[9px] text-[13px] text-slate-700 outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-50 transition-all placeholder-slate-400">
        </div>
        <button type="submit"
            class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
            Filter
        </button>
        @if (request()->anyFilled(['search']))
            <a href="{{ route('general-stores.index') }}"
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
                        <x-sort-th column="name" label="Nama Toko" :currentSort="$sort" :currentDir="$dir" />
                        <x-sort-th column="created_at" label="Ditambahkan" :currentSort="$sort" :currentDir="$dir" />
                        @if (auth()->user()->can('general-stores.edit') || auth()->user()->can('general-stores.delete'))
                            <th
                                class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-center bg-slate-50 border-b border-slate-100">
                                Aksi
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($stores as $store)
                        <tr class="hover:bg-brand-50/40 transition-colors"
                            data-store-name="{{ addslashes($store->name) }}"
                            data-edit-url="{{ route('general-stores.edit', $store) }}"
                            data-delete-url="{{ route('general-stores.destroy', $store) }}"
                            data-can-edit="{{ auth()->user()->can('general-stores.edit') ? 'true' : 'false' }}"
                            data-can-delete="{{ auth()->user()->can('general-stores.delete') ? 'true' : 'false' }}">

                            <td class="px-4 py-3 text-[13px] font-semibold text-slate-800">{{ $store->name }}</td>
                            <td class="px-4 py-3 text-[13px] text-slate-400">{{ $store->created_at->format('d M Y') }}
                            </td>

                            @if (auth()->user()->can('general-stores.edit') || auth()->user()->can('general-stores.delete'))
                                <td class="px-4 py-3 text-center">
                                    <button onclick="toggleActionMenu(event, 'menu-{{ $store->id }}')"
                                        class="w-8 h-8 flex items-center justify-center mx-auto rounded-[7px] text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors text-xl font-bold">
                                        ⋮
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center">
                                <div class="text-2xl mb-2">🏪</div>
                                <div class="text-[14px] font-semibold text-slate-500">Tidak ada toko ditemukan</div>
                                <div class="text-[12px] text-slate-400 mt-1">Tambah manual atau import Excel</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($stores->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="text-[12px] text-slate-400">
                    Menampilkan {{ $stores->firstItem() }}–{{ $stores->lastItem() }} dari {{ $stores->total() }} toko
                </div>
                <div class="flex items-center gap-1.5">
                    @if ($stores->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">‹</span>
                    @else
                        <a href="{{ $stores->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">‹</a>
                    @endif

                    @foreach ($stores->getUrlRange(1, $stores->lastPage()) as $page => $url)
                        @if ($page == $stores->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-brand-600 text-white text-[13px] font-semibold">{{ $page }}</span>
                        @elseif(abs($page - $stores->currentPage()) <= 2)
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($stores->hasMorePages())
                        <a href="{{ $stores->nextPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-500 hover:bg-brand-50 hover:border-brand-200 hover:text-brand-600 text-[13px] transition-colors">›</a>
                    @else
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-[7px] border-[1.5px] border-slate-200 text-slate-300 text-[13px]">›</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Import Modal --}}
    <div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
        style="background:rgba(0,0,0,0.5);backdrop-filter:blur(6px)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <div>
                    <div class="text-[14px] font-bold text-slate-800">Import Toko Umum</div>
                    <div class="text-[12px] text-slate-400 mt-0.5">Upload file Excel (.xlsx / .xls)</div>
                </div>
                <button onclick="closeImportModal()" id="import-close-btn"
                    class="w-8 h-8 flex items-center justify-center rounded-[7px] bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors text-sm">
                    ✕
                </button>
            </div>

            <div class="p-5" id="import-upload-section">
                <div class="border-2 border-dashed border-slate-200 rounded-xl p-8 text-center mb-4" id="drop-zone">
                    <div class="text-3xl mb-3">📂</div>
                    <div class="text-[13px] font-semibold text-slate-700 mb-1">Pilih atau drag file Excel</div>
                    <div class="text-[12px] text-slate-400 mb-4">Format: .xlsx atau .xls · Maks 50MB</div>
                    <label for="import-file"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors cursor-pointer">
                        📁 Pilih File
                    </label>
                    <input type="file" id="import-file" accept=".xlsx,.xls" class="hidden">
                    <div id="selected-file-name" class="mt-3 text-[12px] text-slate-500"></div>
                </div>

                <div class="bg-brand-50 border border-brand-100 rounded-xl p-3 mb-4">
                    <div class="text-[11px] text-brand-700">
                        ℹ️ Format kolom Excel: <span class="font-mono font-semibold">Nama Toko Umum</span>
                    </div>
                </div>

                <button onclick="startImport()" id="start-import-btn"
                    class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors opacity-50 cursor-not-allowed"
                    disabled>
                    Mulai Import
                </button>
            </div>

            <div class="p-5 hidden" id="import-progress-section">
                <div class="flex items-center gap-3 mb-4">
                    <div id="progress-icon" class="text-2xl">⏳</div>
                    <div>
                        <div id="progress-title" class="text-[13px] font-bold text-slate-800">Memproses data...</div>
                        <div id="progress-subtitle" class="text-[12px] text-slate-400 mt-0.5"></div>
                    </div>
                </div>
                <div class="bg-slate-100 rounded-full h-3 mb-2 overflow-hidden">
                    <div id="progress-bar" class="h-full bg-brand-600 rounded-full transition-all duration-500"
                        style="width: 0%"></div>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <div id="progress-text" class="text-[12px] text-slate-500">0%</div>
                    <div id="progress-detail" class="text-[12px] text-slate-400"></div>
                </div>
                <div id="import-result" class="hidden">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-3 mb-4">
                        <div id="result-text" class="text-[13px] font-semibold text-emerald-700"></div>
                    </div>
                    <button onclick="closeImportModal(); window.location.reload();"
                        class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors">
                        Selesai — Refresh Halaman
                    </button>
                </div>
            </div>
        </div>
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
                const storeName = row.dataset.storeName;
                const editUrl = row.dataset.editUrl;
                const deleteUrl = row.dataset.deleteUrl;
                const canEdit = row.dataset.canEdit === 'true';
                const canDelete = row.dataset.canDelete === 'true';

                let menuItems = '';
                if (canEdit) {
                    menuItems += `
                    <a href="${editUrl}"
                        class="flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors font-medium">
                        ✏️ Edit
                    </a>
                `;
                }
                if (canDelete) {
                    if (canEdit) menuItems += `<div class="h-px bg-slate-100"></div>`;
                    menuItems += `
                    <button onclick="openDeleteModal('${deleteUrl}', decodeURIComponent('${encodeURIComponent(storeName)}'))"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-[13px] text-rose-500 hover:bg-rose-50 transition-colors font-medium">
                        🗑️ Hapus
                    </button>
                `;
                }
                if (!menuItems) return;

                const menu = document.createElement('div');
                menu.id = 'floating-' + menuId;
                menu.className = 'fixed z-[9999] bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden';
                menu.style.cssText = `min-width:144px; top:${rect.bottom + 4}px; left:${rect.right - 144}px;`;
                menu.innerHTML = menuItems;
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
                document.getElementById('modal-store-name').textContent = name;
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

            // ── Import ────────────────────────────────────────────────────────
            let importCacheKey = null;
            let progressInterval = null;
            let selectedFile = null;

            function openImportModal() {
                document.getElementById('import-modal').classList.remove('hidden');
                document.getElementById('import-modal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeImportModal() {
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
                document.getElementById('import-modal').classList.add('hidden');
                document.getElementById('import-modal').classList.remove('flex');
                document.body.style.overflow = '';
                resetImportModal();
            }

            function resetImportModal() {
                selectedFile = null;
                importCacheKey = null;
                document.getElementById('import-upload-section').classList.remove('hidden');
                document.getElementById('import-progress-section').classList.add('hidden');
                document.getElementById('import-result').classList.add('hidden');
                document.getElementById('selected-file-name').textContent = '';
                document.getElementById('import-file').value = '';
                document.getElementById('start-import-btn').disabled = true;
                document.getElementById('start-import-btn').classList.add('opacity-50', 'cursor-not-allowed');
                document.getElementById('progress-bar').style.width = '0%';
                document.getElementById('progress-text').textContent = '0%';
                document.getElementById('progress-detail').textContent = '';
                document.getElementById('import-close-btn').disabled = false;
            }

            document.getElementById('import-file').addEventListener('change', function() {
                selectedFile = this.files[0];
                if (selectedFile) {
                    document.getElementById('selected-file-name').textContent = '📄 ' + selectedFile.name;
                    document.getElementById('start-import-btn').disabled = false;
                    document.getElementById('start-import-btn').classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            const dropZone = document.getElementById('drop-zone');
            dropZone.addEventListener('dragover', e => {
                e.preventDefault();
                dropZone.classList.add('border-brand-400', 'bg-brand-50');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('border-brand-400', 'bg-brand-50');
            });
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('border-brand-400', 'bg-brand-50');
                const file = e.dataTransfer.files[0];
                if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls'))) {
                    selectedFile = file;
                    document.getElementById('selected-file-name').textContent = '📄 ' + file.name;
                    document.getElementById('start-import-btn').disabled = false;
                    document.getElementById('start-import-btn').classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });

            async function startImport() {
                if (!selectedFile) return;
                document.getElementById('import-upload-section').classList.add('hidden');
                document.getElementById('import-progress-section').classList.remove('hidden');
                document.getElementById('import-close-btn').disabled = true;

                const formData = new FormData();
                formData.append('file', selectedFile);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                try {
                    const response = await fetch('{{ route('general-stores.import') }}', {
                        method: 'POST',
                        body: formData,
                    });
                    const data = await response.json();
                    if (!data.success) {
                        showImportError(data.message);
                        return;
                    }
                    importCacheKey = data.cache_key;
                    document.getElementById('progress-subtitle').textContent = 'Total ' + data.total_rows.toLocaleString() +
                        ' baris data';
                    progressInterval = setInterval(checkProgress, 1500);
                } catch (err) {
                    showImportError('Gagal menghubungi server. Coba lagi.');
                }
            }

            async function checkProgress() {
                if (!importCacheKey) return;
                try {
                    const response = await fetch('{{ route('general-stores.import.progress') }}?cache_key=' +
                        importCacheKey);
                    const data = await response.json();
                    if (!data.success) return;
                    document.getElementById('progress-bar').style.width = data.percentage + '%';
                    document.getElementById('progress-text').textContent = data.percentage + '%';
                    document.getElementById('progress-detail').textContent = data.done + ' / ' + data.total +
                        ' batch selesai';
                    if (data.is_done) {
                        clearInterval(progressInterval);
                        progressInterval = null;
                        showImportDone(data.rows);
                    }
                } catch (err) {}
            }

            function showImportDone(totalRows) {
                document.getElementById('progress-icon').textContent = '✅';
                document.getElementById('progress-title').textContent = 'Import selesai!';
                document.getElementById('progress-bar').style.width = '100%';
                document.getElementById('progress-bar').classList.remove('bg-brand-600');
                document.getElementById('progress-bar').classList.add('bg-emerald-500');
                document.getElementById('result-text').textContent = totalRows.toLocaleString() + ' toko berhasil diproses.';
                document.getElementById('import-result').classList.remove('hidden');
                document.getElementById('import-close-btn').disabled = false;
            }

            function showImportError(message) {
                clearInterval(progressInterval);
                document.getElementById('import-upload-section').classList.remove('hidden');
                document.getElementById('import-progress-section').classList.add('hidden');
                document.getElementById('import-close-btn').disabled = false;
                alert('❌ ' + message);
            }

            document.getElementById('import-modal').addEventListener('click', function(e) {
                if (e.target === this && !document.getElementById('import-close-btn').disabled) closeImportModal();
            });
        </script>
    @endpush

</x-layouts.app>
