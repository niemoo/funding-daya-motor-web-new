<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromArray;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AttendanceItemTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Template Input Part';
    }

    public function array(): array
    {
        // Contoh data sebagai panduan
        return [
            [1, 'BP-001', 5, 'Contoh catatan'],
            [2, 'SK-101', 3, ''],
            [3, 'FR-202', 10, 'Urgent'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nomor Part', 'Quantity', 'Catatan'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 14,
            'D' => 35,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // ── Header ──
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1D61AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ── Contoh data rows ──
        $sheet->getStyle('A2:D4')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFBEB'], // kuning muda — tanda ini contoh
            ],
            'font' => [
                'color' => ['argb' => 'FFB45309'],
                'size'  => 10,
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center kolom No & Qty
        $sheet->getStyle('A2:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Baris kosong untuk input (baris 5–54 = 50 baris) ──
        $sheet->getStyle('A5:D54')->applyFromArray([
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFFFFF'],
            ],
            'font' => ['size' => 10],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A5:A54')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5:C54')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($row = 5; $row <= 54; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // ── Border seluruh tabel ──
        $sheet->getStyle('A1:D54')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFE2E8F0'],
                ],
            ],
        ]);

        // ── Freeze header ──
        $sheet->freezePane('A2');

        // ── Sheet instruksi ──
        $spreadsheet = $sheet->getParent();
        $instrSheet  = $spreadsheet->createSheet();
        $instrSheet->setTitle('Instruksi');

        $instrSheet->getColumnDimension('A')->setWidth(60);
        $instrSheet->getColumnDimension('B')->setWidth(40);

        $instructions = [
            ['PANDUAN PENGISIAN TEMPLATE', ''],
            ['', ''],
            ['1. Hapus baris contoh (baris berwarna kuning) sebelum upload.', ''],
            ['2. Kolom "No" diisi nomor urut (1, 2, 3, ...).', ''],
            ['3. Kolom "Nomor Part" wajib diisi, tidak boleh kosong.', ''],
            ['4. Kolom "Quantity" wajib diisi dengan angka bulat positif.', ''],
            ['5. Kolom "Catatan" boleh dikosongkan.', ''],
            ['6. Jika ada nomor part yang sama, qty akan dijumlahkan otomatis.', ''],
            ['7. Simpan file dalam format .xlsx sebelum diupload.', ''],
        ];

        foreach ($instructions as $rowIdx => $row) {
            $instrSheet->fromArray($row, null, 'A' . ($rowIdx + 1));
        }

        $instrSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FF1D61AF']],
        ]);

        for ($i = 3; $i <= 9; $i++) {
            $instrSheet->getStyle("A{$i}")->applyFromArray([
                'font' => ['size' => 11, 'color' => ['argb' => 'FF334155']],
            ]);
        }

        return [];
    }
}