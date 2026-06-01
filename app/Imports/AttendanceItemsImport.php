<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class AttendanceItemsImport implements WithMultipleSheets
{
    protected AttendanceItemsSheetImport $sheet;

    public function __construct()
    {
        $this->sheet = new AttendanceItemsSheetImport();
    }

    public function sheets(): array
    {
        return [
            0 => $this->sheet,
        ];
    }

    public function getValidItems(): array
    {
        return $this->sheet->validItems;
    }

    public function getWarnings(): array
    {
        return $this->sheet->warnings;
    }
}

class AttendanceItemsSheetImport implements ToCollection, WithStartRow
{
    public array $validItems = [];
    public array $warnings   = [];

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $merged = [];

        foreach ($rows as $index => $row) {
            $rowNum     = $index + 2;
            $kodePart = trim((string) ($row[1] ?? ''));
            $quantity   = $row[2] ?? null;
            $notes      = trim((string) ($row[3] ?? '')) ?: null;

            // Skip baris kosong
            if (empty($kodePart) && $quantity === null) continue;

            // Validasi kode_part
            if (empty($kodePart)) {
                $this->warnings[] = "Baris {$rowNum}: Nomor part kosong, dilewati.";
                continue;
            }

            // Validasi quantity
            if (!is_numeric($quantity) || intval($quantity) != $quantity || intval($quantity) < 1) {
                $this->warnings[] = "Baris {$rowNum}: Quantity tidak valid (harus angka bulat positif), dilewati.";
                continue;
            }

            // Gabungkan duplikat kode_part
            if (isset($merged[$kodePart])) {
                $merged[$kodePart]['quantity'] += intval($quantity);
            } else {
                $merged[$kodePart] = [
                    'kode_part' => $kodePart,
                    'quantity'    => intval($quantity),
                    'notes'       => $notes,
                ];
            }
        }

        $this->validItems = array_values($merged);
    }
}