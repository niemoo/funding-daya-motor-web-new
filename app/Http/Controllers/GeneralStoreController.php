<?php

namespace App\Http\Controllers;

use App\Jobs\ImportGeneralStoresJob;
use App\Models\GeneralStore;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class GeneralStoreController extends Controller
{
    public function index(Request $request)
    {
        $query = GeneralStore::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sortable = ['name', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'name';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $stores = $query->paginate(15)->withQueryString();

        return view('general-stores.index', compact('stores', 'sort', 'dir'));
    }

    public function create()
    {
        return view('general-stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:general_stores,name',
        ], [
            'name.required' => 'Nama toko wajib diisi.',
            'name.unique'   => 'Nama toko sudah terdaftar.',
        ]);

        GeneralStore::create(['name' => $request->name]);

        return redirect()->route('general-stores.index')
            ->with('success', 'Toko berhasil ditambahkan.');
    }

    public function edit(GeneralStore $generalStore)
    {
        return view('general-stores.edit', compact('generalStore'));
    }

    public function update(Request $request, GeneralStore $generalStore)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:general_stores,name,' . $generalStore->id,
        ], [
            'name.required' => 'Nama toko wajib diisi.',
            'name.unique'   => 'Nama toko sudah terdaftar.',
        ]);

        $generalStore->update(['name' => $request->name]);

        return redirect()->route('general-stores.index')
            ->with('success', 'Toko berhasil diperbarui.');
    }

    public function destroy(GeneralStore $generalStore)
    {
        $generalStore->delete();

        return redirect()->route('general-stores.index')
            ->with('success', 'Toko berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes'    => 'File harus berformat xlsx atau xls.',
            'file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        ini_set('memory_limit', '512M');

        $import = new class implements ToCollection, WithStartRow {
            public $rows;
            public function startRow(): int { return 2; }
            public function collection(Collection $rows) { $this->rows = $rows; }
        };

        Excel::import($import, $request->file('file'));

        $rows = collect($import->rows)
            ->filter(fn($row) => !empty(trim((string)($row[0] ?? ''))))
            ->values();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid di file Excel.',
            ], 422);
        }

        $chunks      = $rows->chunk(500);
        $totalChunks = $chunks->count();
        $cacheKey    = 'import_general_stores_' . auth()->id() . '_' . time();

        Cache::put($cacheKey, [
            'done'  => 0,
            'total' => $totalChunks,
            'rows'  => $rows->count(),
        ], now()->addHour());

        $jobs = $chunks->map(fn($chunk, $index) => new ImportGeneralStoresJob(
            rows: $chunk->values()->toArray(),
            cacheKey: $cacheKey,
            chunkIndex: $index,
            totalChunks: $totalChunks,
        ))->toArray();

        Bus::batch($jobs)
            ->name('Import General Stores - ' . auth()->user()->name)
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'success'    => true,
            'cache_key'  => $cacheKey,
            'total_rows' => $rows->count(),
            'message'    => 'Import dimulai. Memproses ' . $rows->count() . ' baris...',
        ]);
    }

    public function importProgress(Request $request)
    {
        $cacheKey = $request->cache_key;

        if (!$cacheKey) {
            return response()->json(['success' => false, 'message' => 'Cache key tidak valid.'], 422);
        }

        $progress = Cache::get($cacheKey);

        if (!$progress) {
            return response()->json(['success' => false, 'message' => 'Import tidak ditemukan.'], 404);
        }

        $percentage = $progress['total'] > 0
            ? round(($progress['done'] / $progress['total']) * 100)
            : 0;

        return response()->json([
            'success'    => true,
            'done'       => $progress['done'],
            'total'      => $progress['total'],
            'rows'       => $progress['rows'],
            'percentage' => $percentage,
            'is_done'    => $progress['done'] >= $progress['total'],
        ]);
    }
}