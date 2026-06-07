<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Part;
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
        public string $mode,
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) return;

        $branchMap = Branch::pluck('id', 'kode_cabang')->toArray();
        $groupMap  = PartGroup::pluck('id', 'name')->toArray();

        $toInsert = [];

        foreach ($this->rows as $row) {
            \Log::info('Processing row', ['row' => array_slice($row, 0, 5)]);
            $kodeCabang  = trim((string)($row[1] ?? ''));
            $namaCabang  = trim((string)($row[2] ?? ''));
            $kodePart    = trim((string)($row[3] ?? ''));
            $deskripsi   = trim((string)($row[4] ?? ''));
            $lokasiStock = trim((string)($row[5] ?? ''));
            $subCateg    = trim((string)($row[6] ?? ''));
            $jumlahRaw   = trim((string)($row[7] ?? '0'));
            $nilaiRaw    = trim((string)($row[8] ?? '0'));

            if (empty($kodeCabang) || empty($kodePart)) continue;
            if (strtolower($kodePart) === 'totals' || strtolower($kodeCabang) === 'totals') continue;

            $jumlah = $this->parseIndonesianNumber($jumlahRaw);
            $nilai  = $this->parseIndonesianNumber($nilaiRaw);

            // Auto-create branch
            if (!isset($branchMap[$kodeCabang])) {
                $branch = Branch::firstOrCreate(
                    ['kode_cabang' => $kodeCabang],
                    ['nama_cabang' => $namaCabang ?: $kodeCabang]
                );
                $branchMap[$kodeCabang] = $branch->id;
            }

            // Auto-create group
            $partGroupId = null;
            if ($subCateg) {
                if (!isset($groupMap[$subCateg])) {
                    $group = PartGroup::firstOrCreate(['name' => $subCateg]);
                    $groupMap[$subCateg] = $group->id;
                }
                $partGroupId = $groupMap[$subCateg];
            }

            \Log::info('Checking part', ['kode_part' => $kodePart, 'chunk' => $this->chunkIndex]);

            // ← Cek langsung ke DB, bukan dari cache array
            Part::firstOrCreate(
                ['kode_part' => $kodePart],
                [
                    'deskripsi_part' => $deskripsi ?: '—',
                    'part_group_id'  => $partGroupId,
                    'het'            => null,
                ]
            );

            $toInsert[] = [
                'branch_id'     => $branchMap[$kodeCabang],
                'kode_part'     => $kodePart,
                'part_group_id' => $partGroupId,
                'lokasi_stock'  => $lokasiStock ?: null,
                'jumlah'        => $jumlah,
                'nilai_stock'   => $nilai,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if (!empty($toInsert)) {
            if ($this->mode === 'upsert') {
                StockLocator::upsert(
                    $toInsert,
                    ['branch_id', 'kode_part'],
                    ['part_group_id', 'lokasi_stock', 'jumlah', 'nilai_stock', 'updated_at']
                );
            } else {
                StockLocator::insert($toInsert);
            }
        }

        $progress = Cache::get($this->cacheKey, ['done' => 0, 'total' => $this->totalChunks]);
        $progress['done']++;
        Cache::put($this->cacheKey, $progress, now()->addHour());
    }

    private function parseIndonesianNumber(string $value): int
    {
        if (empty($value)) return 0;
        $clean = str_replace('.', '', $value);
        $clean = str_replace(',', '.', $clean);
        $clean = preg_replace('/[^\d.]/', '', $clean);
        return is_numeric($clean) ? (int)(float)$clean : 0;
    }
}