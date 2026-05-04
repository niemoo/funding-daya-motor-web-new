<?php

namespace App\Console\Commands;

use App\Models\Part;
use App\Models\PartGroup;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class ImportPartsCommand extends Command
{
    protected $signature   = 'parts:import {file : Path ke file Excel}';
    protected $description = 'Import master data parts dari file Excel';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        $this->info("Membaca file: {$filePath}");

        // ── 1. Baca semua data dari Excel ─────────────────────────────────
        $import = new class implements ToCollection, WithStartRow {
            public function startRow(): int { return 2; }
            public function collection(Collection $rows) { $this->rows = $rows; }
            public $rows;
        };

        Excel::import($import, $filePath);
        $rows = collect($import->rows)->filter(fn($row) => !empty(trim((string)($row[1] ?? ''))));

        $total = $rows->count();
        $this->info("Total data valid: {$total} baris");

        // ── 2. Kumpulkan semua group unik dari Excel ──────────────────────
        $this->info("Memproses groups...");
        $groupNames = $rows
            ->map(fn($row) => trim((string)($row[3] ?? '')))
            ->filter()
            ->unique()
            ->values();

        // Insert group yang belum ada
        $groupsInserted = 0;
        foreach ($groupNames as $name) {
            PartGroup::firstOrCreate(['name' => $name]);
            $groupsInserted++;
        }
        $this->info("Groups diproses: {$groupsInserted}");

        // Load semua groups ke memory sebagai map name => id
        $groupMap = PartGroup::pluck('id', 'name');

        // ── 3. Insert parts per chunk ─────────────────────────────────────
        $this->info("Mengimport parts...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $inserted  = 0;
        $skipped   = 0;
        $errors    = [];

        // Chunk per 1000 baris untuk efisiensi
        $rows->chunk(1000)->each(function ($chunk) use ($groupMap, $bar, &$inserted, &$skipped, &$errors) {
            $toInsert = [];

            foreach ($chunk as $index => $row) {
                $kodePart     = trim((string)($row[1] ?? ''));
                $deskripsiPart = trim((string)($row[2] ?? ''));
                $groupName    = trim((string)($row[3] ?? ''));

                // Skip kalau kode part kosong
                if (empty($kodePart)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $partGroupId = $groupMap[$groupName] ?? null;

                $toInsert[] = [
                    'kode_part'      => $kodePart,
                    'deskripsi_part' => $deskripsiPart ?: '—',
                    'part_group_id'  => $partGroupId,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                $bar->advance();
            }

            if (!empty($toInsert)) {
                // upsert — kalau kode_part sudah ada, update deskripsi & group
                Part::upsert(
                    $toInsert,
                    ['kode_part'],                              // unique key
                    ['deskripsi_part', 'part_group_id', 'updated_at'] // kolom yang diupdate
                );
                $inserted += count($toInsert);
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Import selesai!");
        $this->table(
            ['Keterangan', 'Jumlah'],
            [
                ['Total baris diproses', $total],
                ['Parts berhasil diimport', $inserted],
                ['Baris dilewati', $skipped],
            ]
        );

        return 0;
    }
}