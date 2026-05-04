<?php

namespace App\Http\Controllers;

use App\Models\PartGroup;
use Illuminate\Http\Request;

class PartGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = PartGroup::withCount('parts');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortable = ['name', 'created_at', 'parts_count'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'name';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $groups = $query->paginate(15)->withQueryString();

        return view('part-groups.index', compact('groups', 'sort', 'dir'));
    }

    public function create()
    {
        return view('part-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:part_groups,name',
        ], [
            'name.required' => 'Nama group wajib diisi.',
            'name.unique'   => 'Nama group sudah terdaftar.',
        ]);

        PartGroup::create(['name' => $request->name]);

        return redirect()->route('part-groups.index')
            ->with('success', 'Group berhasil ditambahkan.');
    }

    public function edit(PartGroup $partGroup)
    {
        return view('part-groups.edit', compact('partGroup'));
    }

    public function update(Request $request, PartGroup $partGroup)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:part_groups,name,' . $partGroup->id,
        ], [
            'name.required' => 'Nama group wajib diisi.',
            'name.unique'   => 'Nama group sudah terdaftar.',
        ]);

        $partGroup->update(['name' => $request->name]);

        return redirect()->route('part-groups.index')
            ->with('success', 'Group berhasil diperbarui.');
    }

    public function destroy(PartGroup $partGroup)
    {
        // Cek apakah masih ada parts yang pakai group ini
        if ($partGroup->parts()->count() > 0) {
            return redirect()->route('part-groups.index')
                ->with('error', 'Group tidak bisa dihapus karena masih digunakan oleh ' . $partGroup->parts()->count() . ' part.');
        }

        $partGroup->delete();

        return redirect()->route('part-groups.index')
            ->with('success', 'Group berhasil dihapus.');
    }
}