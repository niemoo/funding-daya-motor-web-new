<?php

namespace App\Jobs;

use App\Models\Part;
use App\Models\PartGroup;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ImportPartsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;

    public function __construct(
        public array $rows,
        public string $cacheKey,
        public int $chunkIndex,
        public int $totalChunks,
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) return;

        // Load semua groups ke memory
        $groupMap = PartGroup::pluck('id', 'name');

        $toUpsert = [];

        foreach ($this->rows as $row) {
            $kodePart      = trim((string)($row[1] ?? ''));
            $deskripsiPart = trim((string)($row[2] ?? ''));
            $groupName     = trim((string)($row[3] ?? ''));

            if (empty($kodePart)) continue;

            // Auto-create group kalau belum ada
            if ($groupName && !isset($groupMap[$groupName])) {
                $group = PartGroup::firstOrCreate(['name' => $groupName]);
                $groupMap[$groupName] = $group->id;
            }

            $toUpsert[] = [
                'kode_part'      => $kodePart,
                'deskripsi_part' => $deskripsiPart ?: '—',
                'part_group_id'  => $groupName ? ($groupMap[$groupName] ?? null) : null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }

        if (!empty($toUpsert)) {
            Part::upsert(
                $toUpsert,
                ['kode_part'],
                ['deskripsi_part', 'part_group_id', 'updated_at']
            );
        }

        // Update progress di cache
        $progress = Cache::get($this->cacheKey, ['done' => 0, 'total' => $this->totalChunks]);
        $progress['done']++;
        Cache::put($this->cacheKey, $progress, now()->addHour());
    }
}