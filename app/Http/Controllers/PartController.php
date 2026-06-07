<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Jobs\ImportPartsJob;
use App\Models\PartGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

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

        $sortable = ['kode_part', 'deskripsi_part', 'het', 'created_at', 'group'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'kode_part';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';
        if ($sort === 'group') {
            $query->leftJoin('part_groups', 'parts.part_group_id', '=', 'part_groups.id')
                ->select('parts.*')
                ->orderBy('part_groups.name', $dir);
        } else {
            $query->orderBy($sort, $dir);
        }

        $parts  = $query->paginate(15)->withQueryString();
        $groups = PartGroup::orderBy('name')->get();

        return view('parts.index', compact('parts', 'groups', 'sort', 'dir'));
    }

    public function create()
    {
        $groups = PartGroup::orderBy('name')->get();
        return view('parts.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_part'      => 'required|string|max:100|unique:parts,kode_part',
            'deskripsi_part' => 'required|string|max:255',
            'part_group_id'  => 'required|exists:part_groups,id',
            'het'            => 'nullable|integer|min:0',
        ], [
            'kode_part.required'      => 'Kode part wajib diisi.',
            'kode_part.unique'        => 'Kode part sudah terdaftar.',
            'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
            'part_group_id.required'  => 'Group wajib dipilih.',
            'part_group_id.exists'    => 'Group tidak valid.',
            'het.integer'             => 'HET harus berupa angka.',
            'het.min'                 => 'HET tidak boleh negatif.',
        ]);

        Part::create($request->only(['kode_part', 'deskripsi_part', 'part_group_id', 'het']));

        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil ditambahkan.');
    }

    public function edit(Part $part)
    {
        $groups = PartGroup::orderBy('name')->get();
        return view('parts.edit', compact('part', 'groups'));
    }

    public function update(Request $request, Part $part)
    {
        $request->validate([
            'kode_part'      => 'required|string|max:100|unique:parts,kode_part,' . $part->id,
            'deskripsi_part' => 'required|string|max:255',
            'part_group_id'  => 'required|exists:part_groups,id',
            'het'            => 'nullable|integer|min:0',
        ], [
            'kode_part.required'      => 'Kode part wajib diisi.',
            'kode_part.unique'        => 'Kode part sudah terdaftar.',
            'deskripsi_part.required' => 'Deskripsi part wajib diisi.',
            'part_group_id.required'  => 'Group wajib dipilih.',
            'part_group_id.exists'    => 'Group tidak valid.',
            'het.integer'             => 'HET harus berupa angka.',
            'het.min'                 => 'HET tidak boleh negatif.',
        ]);

        $part->update($request->only(['kode_part', 'deskripsi_part', 'part_group_id', 'het']));

        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil diperbarui.');
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return redirect()->route('parts.index')
            ->with('success', 'Part berhasil dihapus.');
    }

    // ── Upload & Dispatch Import ──────────────────────────────────────────
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:51200', // max 50MB
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes'    => 'File harus berformat xlsx atau xls.',
            'file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        // Baca semua rows dari Excel
        $import = new class implements ToCollection, WithStartRow {
            public $rows;
            public function startRow(): int { return 2; }
            public function collection(Collection $rows) { $this->rows = $rows; }
        };

        ini_set('memory_limit', '512M');
        \Log::info('Memory limit: ' . ini_get('memory_limit'));
        Excel::import($import, $request->file('file'));

        $rows = collect($import->rows)
            ->filter(fn($row) => !empty(trim((string)($row[1] ?? ''))))
            ->values();

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data valid di file Excel.',
            ], 422);
        }

        // Bagi ke chunks per 500 baris
        $chunks     = $rows->chunk(500);
        $totalChunks = $chunks->count();
        $cacheKey   = 'import_parts_' . auth()->id() . '_' . time();

        // Init progress di cache
        Cache::put($cacheKey, [
            'done'  => 0,
            'total' => $totalChunks,
            'rows'  => $rows->count(),
        ], now()->addHour());

        // Dispatch semua jobs sebagai batch
        $jobs = $chunks->map(fn($chunk, $index) => new ImportPartsJob(
            rows: $chunk->values()->toArray(),
            cacheKey: $cacheKey,
            chunkIndex: $index,
            totalChunks: $totalChunks,
        ))->toArray();

        Bus::batch($jobs)
            ->name('Import Parts - ' . auth()->user()->name)
            ->allowFailures()
            ->dispatch();

        return response()->json([
            'success'   => true,
            'cache_key' => $cacheKey,
            'total_rows'=> $rows->count(),
            'message'   => 'Import dimulai. Memproses ' . $rows->count() . ' baris...',
        ]);
    }

    // ── Cek Progress ──────────────────────────────────────────────────────
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

        $isDone = $progress['done'] >= $progress['total'];

        return response()->json([
            'success'    => true,
            'done'       => $progress['done'],
            'total'      => $progress['total'],
            'rows'       => $progress['rows'],
            'percentage' => $percentage,
            'is_done'    => $isDone,
        ]);
    }

    public function autocomplete(Request $request)
    {
        $keyword = $request->q;
        if (!$keyword || strlen($keyword) < 3) return response()->json([]);

        $parts = Part::with('group')
            ->where(function($q) use ($keyword) {
                $q->where('kode_part', 'like', '%' . $keyword . '%')
                ->orWhere('deskripsi_part', 'like', '%' . $keyword . '%');
            })
            ->limit(10)
            ->get(['kode_part', 'deskripsi_part', 'part_group_id']);

        return response()->json($parts->map(fn($p) => [
            'kode_part'      => $p->kode_part,
            'deskripsi_part' => $p->deskripsi_part,
            'part_group_id'  => $p->part_group_id,
            'group_name'     => $p->group?->name,
        ]));
    }
}