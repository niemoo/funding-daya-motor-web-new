<?php

namespace App\Http\Controllers;

use App\Exports\StockLocatorTemplateExport;
use App\Jobs\ImportStockLocatorJob;
use App\Models\Branch;
use App\Models\PartGroup;
use App\Models\StockLocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class StockLocatorController extends Controller
{
    public function index(Request $request)
    {
        $query = StockLocator::with(['branch', 'group'])
            ->whereNull('stock_locators.deleted_at');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_part', 'like', '%' . $request->search . '%')
                  ->orWhereHas('part', fn($p) => $p->where('deskripsi_part', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('group_id')) {
            $query->where('part_group_id', $request->group_id);
        }

        $sortable = ['kode_part', 'lokasi_stock', 'jumlah', 'nilai_stock', 'created_at'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'kode_part';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        $query->orderBy($sort, $dir);

        $locators = $query->paginate(20)->withQueryString();
        $branches = Branch::orderBy('nama_cabang')->get();
        $groups   = PartGroup::orderBy('name')->get();

        return view('stock-locators.index', compact('locators', 'branches', 'groups', 'sort', 'dir'));
    }

    public function create()
    {
        $branches = Branch::orderBy('nama_cabang')->get();
        $groups   = PartGroup::orderBy('name')->get();
        return view('stock-locators.create', compact('branches', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'kode_part'     => 'required|string|max:100|exists:parts,kode_part',
            'part_group_id' => 'nullable|exists:part_groups,id',
            'lokasi_stock'  => 'nullable|string|max:100',
            'jumlah'        => 'required|numeric|min:0',
            'nilai_stock'   => 'required|numeric|min:0',
        ], [
            'branch_id.required'  => 'Cabang wajib dipilih.',
            'kode_part.required'  => 'Kode part wajib diisi.',
            'kode_part.exists'    => 'Kode part tidak terdaftar di master part.',
            'jumlah.required'     => 'Jumlah wajib diisi.',
            'nilai_stock.required'=> 'Nilai stock wajib diisi.',
        ]);

        StockLocator::create($request->only([
            'branch_id', 'kode_part', 'part_group_id',
            'lokasi_stock', 'jumlah', 'nilai_stock',
        ]));

        return redirect()->route('stock-locators.index')
            ->with('success', 'Data stock berhasil ditambahkan.');
    }

    public function edit(StockLocator $stockLocator)
    {
        $branches = Branch::orderBy('nama_cabang')->get();
        $groups   = PartGroup::orderBy('name')->get();
        return view('stock-locators.edit', compact('stockLocator', 'branches', 'groups'));
    }

    public function update(Request $request, StockLocator $stockLocator)
    {
        $request->validate([
            'branch_id'     => 'required|exists:branches,id',
            'kode_part'     => 'required|string|max:100|exists:parts,kode_part',
            'part_group_id' => 'nullable|exists:part_groups,id',
            'lokasi_stock'  => 'nullable|string|max:100',
            'jumlah'        => 'required|numeric|min:0',
            'nilai_stock'   => 'required|numeric|min:0',
        ], [
            'branch_id.required'  => 'Cabang wajib dipilih.',
            'kode_part.required'  => 'Kode part wajib diisi.',
            'kode_part.exists'    => 'Kode part tidak terdaftar di master part.',
            'jumlah.required'     => 'Jumlah wajib diisi.',
            'nilai_stock.required'=> 'Nilai stock wajib diisi.',
        ]);

        $stockLocator->update($request->only([
            'branch_id', 'kode_part', 'part_group_id',
            'lokasi_stock', 'jumlah', 'nilai_stock',
        ]));

        return redirect()->route('stock-locators.index')
            ->with('success', 'Data stock berhasil diperbarui.');
    }

    public function destroy(StockLocator $stockLocator)
    {
        $stockLocator->delete();
        return redirect()->route('stock-locators.index')
            ->with('success', 'Data stock berhasil dihapus.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
            'mode' => 'required|in:replace,upsert',
        ]);

        ini_set('memory_limit', '512M');

        $import = new class implements ToCollection, WithStartRow {
            public $rows;
            public function startRow(): int { return 2; }
            public function collection(Collection $rows) { $this->rows = $rows; }
        };

        Excel::import($import, $request->file('file'));

        $rows = collect($import->rows)
            ->filter(fn($row) => !empty(trim((string)($row[1] ?? ''))) && !empty(trim((string)($row[3] ?? ''))))
            ->values();

        if ($rows->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data valid di file Excel.'], 422);
        }

        $chunks      = $rows->chunk(500);
        $totalChunks = $chunks->count();
        $cacheKey    = 'import_stock_locator_' . auth()->id() . '_' . time();
        $mode        = $request->mode;

        Cache::put($cacheKey, [
            'done'  => 0,
            'total' => $totalChunks,
            'rows'  => $rows->count(),
        ], now()->addHour());

        // Kalau replace — soft delete semua data lama dulu
        if ($mode === 'replace') {
            StockLocator::query()->delete();
        }

        $jobs = $chunks->map(fn($chunk, $index) => new ImportStockLocatorJob(
            rows: $chunk->values()->toArray(),
            cacheKey: $cacheKey,
            chunkIndex: $index,
            totalChunks: $totalChunks,
            mode: $mode,
        ))->toArray();

        Bus::batch($jobs)
            ->name('Import Stock Locator - ' . auth()->user()->name)
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
        if (!$cacheKey) return response()->json(['success' => false], 422);

        $progress = Cache::get($cacheKey);
        if (!$progress) return response()->json(['success' => false, 'message' => 'Import tidak ditemukan.'], 404);

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

    public function downloadTemplate()
    {
        return Excel::download(
            new StockLocatorTemplateExport(),
            'template-stock-locator.xlsx'
        );
    }
}