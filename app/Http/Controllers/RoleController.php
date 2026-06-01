<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Permission groups untuk tampilan UI
    private function permissionGroups(): array
    {
        return [
            'Dashboard'   => ['dashboard.view'],
            'Users'       => ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.export'],
            'Roles'       => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
            'Absensi' => ['attendances.view', 'attendances.view_all', 'attendances.edit', 'attendances.delete', 'attendances.export', 'attendances.invoice'],
            'Grup Part' => ['part-groups.view', 'part-groups.create', 'part-groups.edit', 'part-groups.delete'],
            'Parts'       => ['parts.view', 'parts.create', 'parts.edit', 'parts.delete', 'parts.import'],
            'Toko Umum' => ['general-stores.view', 'general-stores.create', 'general-stores.edit', 'general-stores.delete', 'general-stores.import'],
            'Profile'     => ['profile.view'],
        ];
    }

    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = $this->permissionGroups();
        return view('roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah terdaftar.',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        $permissionGroups    = $this->permissionGroups();
        $rolePermissions     = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'permissionGroups', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ], [
            'name.required' => 'Nama role wajib diisi.',
            'name.unique'   => 'Nama role sudah terdaftar.',
        ]);

        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions ?? []);

        // Clear cached permissions untuk semua user dengan role ini
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Role tidak bisa dihapus karena masih digunakan oleh ' . $role->users()->count() . ' user.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}