<x-layouts.app title="Edit Role">
    <div class="max-w-2xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('roles.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">←</a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit Role</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">{{ $role->name }} · {{ $role->permissions->count() }}
                    permissions aktif</p>
            </div>
        </div>

        <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama Role --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <label class="block text-[12px] font-semibold text-slate-600 mb-1.5">Nama Role</label>
                <input type="text" name="name" value="{{ old('name', $role->name) }}"
                    placeholder="Contoh: Manager, Supervisor"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[13px] text-slate-800 outline-none transition-all placeholder-slate-400
                    {{ $errors->has('name') ? 'border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                @error('name')
                    <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Permissions --}}
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="text-[14px] font-bold text-slate-800">Permissions</div>
                        <div class="text-[12px] text-slate-400 mt-0.5">Atur akses yang diberikan ke role ini</div>
                    </div>
                    <button type="button" onclick="toggleAll()"
                        class="text-[12px] font-semibold text-brand-600 hover:text-brand-700 transition-colors">
                        Pilih Semua
                    </button>
                </div>

                <div class="divide-y divide-slate-50">
                    @foreach ($permissionGroups as $group => $permissions)
                        <div class="px-5 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="text-[12px] font-bold text-slate-700 uppercase tracking-wide">
                                        {{ $group }}</div>
                                    @php
                                        $groupActive = count(array_intersect($permissions, $rolePermissions));
                                        $groupTotal = count($permissions);
                                    @endphp
                                    <span
                                        class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                                        {{ $groupActive === $groupTotal ? 'bg-emerald-50 text-emerald-600' : ($groupActive > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-400') }}">
                                        {{ $groupActive }}/{{ $groupTotal }}
                                    </span>
                                </div>
                                <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}')"
                                    class="text-[11px] font-semibold text-slate-400 hover:text-brand-600 transition-colors">
                                    Pilih semua
                                </button>
                            </div>
                            <div class="grid grid-cols-2 gap-2" id="group-{{ Str::slug($group) }}">
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2.5 cursor-pointer group">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission }}"
                                            class="perm-checkbox w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500 transition-colors"
                                            {{ in_array($permission, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                        <span
                                            class="text-[12px] text-slate-600 group-hover:text-slate-800 transition-colors font-mono">
                                            {{ $permission }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                    style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                    Simpan Perubahan
                </button>
                <a href="{{ route('roles.index') }}"
                    class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function toggleAll() {
                const checkboxes = document.querySelectorAll('.perm-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
            }

            function toggleGroup(groupSlug) {
                const group = document.getElementById('group-' + groupSlug);
                const checkboxes = group.querySelectorAll('.perm-checkbox');
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
            }
        </script>
    @endpush
</x-layouts.app>
