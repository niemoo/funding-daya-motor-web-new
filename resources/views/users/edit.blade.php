<x-layouts.app title="Edit User">

    <div class="max-w-xl">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('users.index') }}"
                class="w-8 h-8 flex items-center justify-center rounded-[9px] bg-white border-[1.5px] border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors text-sm">
                ←
            </a>
            <div>
                <h1 class="text-[20px] font-extrabold text-slate-800 tracking-tight">Edit User</h1>
                <p class="text-[13px] text-slate-400 mt-0.5">Ubah data {{ $user->name }}</p>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div>
                    <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        placeholder="Masukkan nama lengkap"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400
                       {{ $errors->has('name') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('name')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        placeholder="nama@email.com"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400
                       {{ $errors->has('email') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                    @error('email')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Role</label>
                    <select name="role_id"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all
                        {{ $errors->has('role_id') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        <option value="">— Pilih Role —</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Divider --}}
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-[12px] text-slate-400 mb-4">
                        🔒 Kosongkan field password jika tidak ingin mengubah password
                    </p>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Password Baru</label>
                        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400
                           {{ $errors->has('password') ? 'border-rose-400 focus:border-rose-400 focus:ring-2 focus:ring-rose-50' : 'border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50' }}">
                        @error('password')
                            <p class="mt-1.5 text-[12px] text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-600 mb-1.5">Konfirmasi Password
                            Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border-[1.5px] rounded-[10px] text-[14px] text-slate-800 outline-none transition-all placeholder-slate-400 border-slate-200 focus:border-brand-600 focus:ring-2 focus:ring-brand-50">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-[13px] font-semibold rounded-[9px] transition-colors"
                        style="box-shadow: 0 3px 10px rgba(29,97,175,0.25)">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('users.index') }}"
                        class="px-5 py-2.5 bg-white border-[1.5px] border-slate-200 text-slate-500 hover:text-slate-700 hover:bg-slate-50 text-[13px] font-semibold rounded-[9px] transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
