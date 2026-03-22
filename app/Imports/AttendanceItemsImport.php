<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AttendanceItemsImport implements ToCollection, WithHeadingRow
{
    protected array $validItems = [];
    protected array $warnings   = [];

    public function collection(Collection $rows)
    {
        $merged = [];

        foreach ($rows as $index => $row) {
            $rowNum     = $index + 2; // +2 karena baris 1 header
            $partNumber = trim($row['nomor_part'] ?? '');
            $quantity   = $row['quantity'] ?? null;
            $notes      = trim($row['catatan'] ?? '') ?: null;

            // Skip baris kosong
            if (empty($partNumber) && empty($quantity)) continue;

            // Validasi part_number
            if (empty($partNumber)) {
                $this->warnings[] = "Baris {$rowNum}: Nomor part kosong, dilewati.";
                continue;
            }

            // Validasi quantity
            if (!is_numeric($quantity) || intval($quantity) != $quantity || intval($quantity) < 1) {
                $this->warnings[] = "Baris {$rowNum}: Quantity tidak valid (harus angka bulat positif), dilewati.";
                continue;
            }

            // Gabungkan duplikat part_number
            if (isset($merged[$partNumber])) {
                $merged[$partNumber]['quantity'] += intval($quantity);
            } else {
                $merged[$partNumber] = [
                    'part_number' => $partNumber,
                    'quantity'    => intval($quantity),
                    'notes'       => $notes,
                ];
            }
        }

        $this->validItems = array_values($merged);
    }

    public function getValidItems(): array
    {
        return $this->validItems;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }
}