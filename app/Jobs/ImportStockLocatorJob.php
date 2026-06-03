<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\PartGroup;
use App\Models\StockLocator;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ImportStockLocatorJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public array  $rows,
        public string $cacheKey,
        public int    $chunkIndex,
        public int    $totalChunks,
        public string $mode, // 'replace' atau 'upsert'
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) return;

        // Load semua branches dan groups ke memory
        $branchMap = Branch::pluck('id', 'kode_cabang')->toArray();
        $groupMap  = PartGroup::pluck('id', 'name')->toArray();

        $toInsert = [];

        foreach ($this->rows as $row) {
            $kodeCabang  = trim((string)($row[1] ?? ''));
            $namaCabang  = trim((string)($row[2] ?? ''));
            $kodePart    = trim((string)($row[3] ?? ''));
            $lokasiStock = trim((string)($row[5] ?? ''));
            $subCateg    = trim((string)($row[6] ?? ''));
            $jumlahRaw   = preg_replace('/[^\d.]/', '', (string)($row[7] ?? '0'));
            $nilaiRaw    = preg_replace('/[^\d.]/', '', (string)($row[8] ?? '0'));

            if (empty($kodeCabang) || empty($kodePart)) continue;

            // Auto-create branch kalau belum ada
            if (!isset($branchMap[$kodeCabang])) {
                $branch = Branch::firstOrCreate(
                    ['kode_cabang' => $kodeCabang],
                    ['nama_cabang' => $namaCabang ?: $kodeCabang]
                );
                $branchMap[$kodeCabang] = $branch->id;
            }

            // Auto-create group kalau belum ada
            $partGroupId = null;
            if ($subCateg) {
                if (!isset($groupMap[$subCateg])) {
                    $group = PartGroup::firstOrCreate(['name' => $subCateg]);
                    $groupMap[$subCateg] = $group->id;
                }
                $partGroupId = $groupMap[$subCateg];
            }

            $toInsert[] = [
                'branch_id'     => $branchMap[$kodeCabang],
                'kode_part'     => $kodePart,
                'part_group_id' => $partGroupId,
                'lokasi_stock'  => $lokasiStock ?: null,
                'jumlah'        => is_numeric($jumlahRaw) ? (float)$jumlahRaw : 0,
                'nilai_stock'   => is_numeric($nilaiRaw) ? (float)$nilaiRaw : 0,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if (!empty($toInsert)) {
            if ($this->mode === 'upsert') {
                // Update kalau branch_id + kode_part sudah ada
                StockLocator::upsert(
                    $toInsert,
                    ['branch_id', 'kode_part'],
                    ['part_group_id', 'lokasi_stock', 'jumlah', 'nilai_stock', 'updated_at']
                );
            } else {
                // Replace — insert saja (data lama sudah di-truncate sebelum batch)
                StockLocator::insert($toInsert);
            }
        }

        $progress = Cache::get($this->cacheKey, ['done' => 0, 'total' => $this->totalChunks]);
        $progress['done']++;
        Cache::put($this->cacheKey, $progress, now()->addHour());
    }
}