<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockLocatorTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'No',
            'Kode Cabang',
            'Nama Cabang',
            'Product Name',
            'Description',
            'Lokasi Stock',
            'Sub Categ',
            'Jumlah',
            'Nilai Stock Akunting',
            'Total',
        ];
    }

    public function array(): array
    {
        // Contoh data
        return [
            [1, 'BEK03', 'KALIMALANG', '50500KEV880', 'STAND MAIN', 'A1 POJOK', 'HGP', 1, 114865, 114865],
            [2, 'BEK03', 'KALIMALANG', '43125KPH903', 'SHOE COMP BRAKE (NA)', 'A1.01.1.1', 'HGP', 6, 27370, 164219],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1D61AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 14,
            'C' => 16,
            'D' => 18,
            'E' => 25,
            'F' => 14,
            'G' => 12,
            'H' => 10,
            'I' => 22,
            'J' => 14,
        ];
    }
}