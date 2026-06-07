<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::withCount('stockLocators');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_cabang', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_cabang', 'like', '%' . $request->search . '%');
            });
        }

        $sortable = ['kode_cabang', 'nama_cabang', 'created_at', 'stock_locators_count'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'nama_cabang';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $branches = $query->paginate(15)->withQueryString();

        return view('branches.index', compact('branches', 'sort', 'dir'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:50|unique:branches,kode_cabang',
            'nama_cabang' => 'required|string|max:255',
        ], [
            'kode_cabang.required' => 'Kode cabang wajib diisi.',
            'kode_cabang.unique'   => 'Kode cabang sudah terdaftar.',
            'nama_cabang.required' => 'Nama cabang wajib diisi.',
        ]);

        Branch::create($request->only(['kode_cabang', 'nama_cabang']));

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'kode_cabang' => 'required|string|max:50|unique:branches,kode_cabang,' . $branch->id,
            'nama_cabang' => 'required|string|max:255',
        ], [
            'kode_cabang.required' => 'Kode cabang wajib diisi.',
            'kode_cabang.unique'   => 'Kode cabang sudah terdaftar.',
            'nama_cabang.required' => 'Nama cabang wajib diisi.',
        ]);

        $branch->update($request->only(['kode_cabang', 'nama_cabang']));

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->stockLocators()->count() > 0) {
            return redirect()->route('branches.index')
                ->with('error', 'Cabang tidak bisa dihapus karena masih memiliki ' . $branch->stockLocators()->count() . ' data stock.');
        }

        $branch->delete();

        return redirect()->route('branches.index')
            ->with('success', 'Cabang berhasil dihapus.');
    }
}