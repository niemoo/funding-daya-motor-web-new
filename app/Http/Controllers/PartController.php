<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function index(Request $request)
    {
        $query = Part::with('group');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_part', 'like', '%' . $request->search . '%')
                ->orWhere('deskripsi_part', 'like', '%' . $request->search . '%')
                ->orWhereHas('group', fn($g) => $g->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->filled('group_id')) {
            $query->where('part_group_id', $request->group_id);
        }

        $sortable = ['kode_part', 'deskripsi_part', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'kode_part';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $parts  = $query->paginate(15)->withQueryString();
        $groups = \App\Models\PartGroup::orderBy('name')->get();

        return view('parts.index', compact('parts', 'groups', 'sort', 'dir'));
    }

    public function create()
    {
        $groups = \App\Models\PartGroup::orderBy('name')->get();
        return view('parts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_part'      => 'required|string|max:100|unique:parts,kode_part',
            'deskripsi_part' => 'required|string|max:255',
            'part_group_id'  => 'required|exists:part_groups,id',
        ], [
            'kode_part.required'      => 'Kode part wajib diisi.',
            'kode_part.unique'        => 'Kode part sudah terdaftar.',
            'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
            'part_group_id.required'  => 'Group wajib dipilih.',
            'part_group_id.exists'    => 'Group tidak valid.',
        ]);

        Part::create($request->only(['kode_part', 'deskripsi_part', 'part_group_id']));

        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil ditambahkan.');
    }

    public function edit(Part $part)
    {
        $groups = \App\Models\PartGroup::orderBy('name')->get();
        return view('parts.edit', compact('part', 'groups'));
    }

    public function update(Request $request, Part $part)
    {
        $request->validate([
            'kode_part'      => 'required|string|max:100|unique:parts,kode_part,' . $part->id,
            'deskripsi_part' => 'required|string|max:255',
            'part_group_id'  => 'required|exists:part_groups,id',
        ], [
            'kode_part.required'      => 'Kode part wajib diisi.',
            'kode_part.unique'        => 'Kode part sudah terdaftar.',
            'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
            'part_group_id.required'  => 'Group wajib dipilih.',
            'part_group_id.exists'    => 'Group tidak valid.',
        ]);

        $part->update($request->only(['kode_part', 'deskripsi_part', 'part_group_id']));

        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil diperbarui.');
    }
    
    // public function index(Request $request)
    // {
    //     $query = Part::query();

    //     if ($request->filled('search')) {
    //         $query->where(function ($q) use ($request) {
    //             $q->where('kode_part', 'like', '%' . $request->search . '%')
    //               ->orWhere('deskripsi_part', 'like', '%' . $request->search . '%')
    //               ->orWhere('group', 'like', '%' . $request->search . '%');
    //         });
    //     }

    //     if ($request->filled('group')) {
    //         $query->where('group', $request->group);
    //     }

    //     $sortable = ['kode_part', 'deskripsi_part', 'group', 'created_at'];
    //     $sort = in_array($request->sort, $sortable) ? $request->sort : 'kode_part';
    //     $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
    //     $query->orderBy($sort, $dir);

    //     $parts  = $query->paginate(15)->withQueryString();
    //     $groups = Part::distinct()->orderBy('group')->pluck('group');

    //     return view('parts.index', compact('parts', 'groups', 'sort', 'dir'));
    // }

    // public function create()
    // {
    //     $groups = Part::distinct()->orderBy('group')->pluck('group');
    //     return view('parts.create', compact('groups'));
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'kode_part'     => 'required|string|max:100|unique:parts,kode_part',
    //         'deskripsi_part'=> 'required|string|max:255',
    //         'group'         => 'required|string|max:100',
    //     ], [
    //         'kode_part.required'      => 'Kode part wajib diisi.',
    //         'kode_part.unique'        => 'Kode part sudah terdaftar.',
    //         'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
    //         'group.required'          => 'Group wajib diisi.',
    //     ]);

    //     Part::create($request->only(['kode_part', 'deskripsi_part', 'group']));

    //     return redirect()->route('parts.index')
    //         ->with('success', 'Part berhasil ditambahkan.');
    // }

    // public function edit(Part $part)
    // {
    //     $groups = Part::distinct()->orderBy('group')->pluck('group');
    //     return view('parts.edit', compact('part', 'groups'));
    // }

    // public function update(Request $request, Part $part)
    // {
    //     $request->validate([
    //         'kode_part'     => 'required|string|max:100|unique:parts,kode_part,' . $part->id,
    //         'deskripsi_part'=> 'required|string|max:255',
    //         'group'         => 'required|string|max:100',
    //     ], [
    //         'kode_part.required'      => 'Kode part wajib diisi.',
    //         'kode_part.unique'        => 'Kode part sudah terdaftar.',
    //         'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
    //         'group.required'          => 'Group wajib diisi.',
    //     ]);

    //     $part->update($request->only(['kode_part', 'deskripsi_part', 'group']));

    //     return redirect()->route('parts.index')
    //         ->with('success', 'Part berhasil diperbarui.');
    // }

    public function destroy(Part $part)
    {
        $part->delete();
        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil dihapus.');
    }
}