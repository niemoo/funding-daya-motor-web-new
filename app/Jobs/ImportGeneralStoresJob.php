<?php

namespace App\Jobs;

use App\Models\GeneralStore;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class ImportGeneralStoresJob implements ShouldQueue
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

        $toUpsert = [];

        foreach ($this->rows as $row) {
            $name = trim((string)($row[0] ?? ''));
            if (empty($name)) continue;

            $toUpsert[] = [
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($toUpsert)) {
            GeneralStore::upsert(
                $toUpsert,
                ['name'],
                ['updated_at']
            );
        }

        $progress = Cache::get($this->cacheKey, ['done' => 0, 'total' => $this->totalChunks]);
        $progress['done']++;
        Cache::put($this->cacheKey, $progress, now()->addHour());
    }
}