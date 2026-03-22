<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class AttendancesExport implements WithMultipleSheets
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        return [
            new AttendancesMainSheet($this->filters),
            new AttendancesItemsSheet($this->filters),
        ];
    }
}

// ─── Sheet 1: Data Absensi ────────────────────────────────────────────────────

class AttendancesMainSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected array $filters;
    protected int $rowNumber = 0;
    protected array $pendingHyperlinks = [];

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Data Absensi';
    }

    public function query()
    {
        $query = Attendance::with(['user', 'items']);

        if (!empty($this->filters['user_auth_id']) && !empty($this->filters['is_admin']) && !$this->filters['is_admin']) {
            $query->where('user_id', $this->filters['user_auth_id']);
        }
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('store_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('person_in_charge_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->filters['search'] . '%'));
            });
        }
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['date'])) {
            $query->whereDate('attendance_date', $this->filters['date']);
        }
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'done') {
                $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
            } elseif ($this->filters['status'] === 'ongoing') {
                $query->whereNull('checkout_time');
            } elseif ($this->filters['status'] === 'auto_checkout') {
                $query->where('is_auto_checkout', true);
            }
        }

        return $query->orderBy('attendance_date', 'desc')->orderBy('checkin_time', 'desc');
    }

    public function headings(): array
    {
        return [
            'No', 'ID Kehadiran', 'Sales', 'Toko', 'Nama PIC', 'No. Telepon PIC',
            'Tanggal', 'Check-in', 'Lokasi Check-in', 'Foto Check-in',
            'Check-out', 'Lokasi Check-out', 'Foto Check-out',
            'Durasi', 'Status', 'Jumlah Part',
        ];
    }

    public function map($att): array
    {
        $this->rowNumber++;
        $currentRow = $this->rowNumber + 1;

        if ($att->work_duration_minutes && !$att->is_auto_checkout) {
            $hours  = intdiv($att->work_duration_minutes, 60);
            $mins   = $att->work_duration_minutes % 60;
            $durasi = ($hours > 0 ? $hours . ' jam ' : '') . $mins . ' menit';
        } elseif (!$att->checkout_time) {
            $durasi = 'Berlangsung';
        } else {
            $durasi = '—';
        }

        if ($att->is_auto_checkout) {
            $status = 'Tidak Checkout';
        } elseif ($att->checkout_time) {
            $status = 'Selesai';
        } else {
            $status = 'Di Lapangan';
        }

        $checkinMapsUrl  = "https://www.google.com/maps?q={$att->checkin_latitude},{$att->checkin_longitude}";
        $checkoutMapsUrl = $att->checkout_latitude
            ? "https://www.google.com/maps?q={$att->checkout_latitude},{$att->checkout_longitude}"
            : null;

        $this->pendingHyperlinks[$currentRow] = [
            'checkin_maps'   => ['col' => 'I', 'url' => $checkinMapsUrl],
            'checkin_photo'  => ['col' => 'J', 'url' => $att->checkin_photo],
            'checkout_maps'  => ['col' => 'L', 'url' => $checkoutMapsUrl],
            'checkout_photo' => ['col' => 'M', 'url' => $att->checkout_photo ?? null],
        ];

        return [
            $this->rowNumber,
            $att->id,
            $att->user->name,
            $att->store_name,
            $att->person_in_charge_name,
            $att->person_in_charge_phone,
            $att->attendance_date->format('d/m/Y'),
            $att->checkin_time->format('H:i'),
            'Lihat Lokasi ↗',
            'Lihat Foto ↗',
            $att->checkout_time ? $att->checkout_time->format('H:i') : '—',
            $checkoutMapsUrl         ? 'Lihat Lokasi ↗' : '—',
            $att->checkout_photo     ? 'Lihat Foto ↗'   : '—',
            $durasi,
            $status,
            $att->items->count() > 0 ? $att->items->count() . ' part' : '—',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 14, 'C' => 22, 'D' => 28, 'E' => 22, 'F' => 20,
            'G' => 14, 'H' => 12, 'I' => 18, 'J' => 16, 'K' => 12,
            'L' => 18, 'M' => 16, 'N' => 16, 'O' => 16, 'P' => 14,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowNumber + 1;

        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D61AF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($lastRow > 1) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $fillColor = ($row % 2 === 0) ? 'FFFAFBFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$row}:P{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'font'      => ['size' => 10],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
            }

            // Center: No, ID Kehadiran, Tanggal dst
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:P{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Status coloring — kolom O
            for ($row = 2; $row <= $lastRow; $row++) {
                $status = $sheet->getCell("O{$row}")->getValue();
                if ($status === 'Selesai') {
                    $sheet->getStyle("O{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FF059669'], 'bold' => true]]);
                } elseif ($status === 'Di Lapangan') {
                    $sheet->getStyle("O{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true]]);
                } elseif ($status === 'Tidak Checkout') {
                    $sheet->getStyle("O{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FFEF4444'], 'bold' => true]]);
                }
            }

            foreach ($this->pendingHyperlinks as $row => $links) {
                foreach ($links as $link) {
                    if (!$link['url']) continue;
                    $cell = $sheet->getCell($link['col'] . $row);
                    $cell->setHyperlink(new Hyperlink($link['url']));
                    $sheet->getStyle($link['col'] . $row)->applyFromArray([
                        'font' => ['color' => ['argb' => 'FF1D61AF'], 'underline' => true, 'size' => 10],
                    ]);
                }
            }

            $sheet->getStyle("A1:P{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:P1");

        return [];
    }
}

// ─── Sheet 2: Daftar Part ─────────────────────────────────────────────────────

class AttendancesItemsSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected array $filters;
    protected int $rowNumber = 0;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'Daftar Part';
    }

    public function query()
    {
        $query = Attendance::with(['user', 'items'])
            ->has('items');

        if (!empty($this->filters['user_auth_id']) && !empty($this->filters['is_admin']) && !$this->filters['is_admin']) {
            $query->where('user_id', $this->filters['user_auth_id']);
        }
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('store_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('person_in_charge_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->filters['search'] . '%'));
            });
        }
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }
        if (!empty($this->filters['date'])) {
            $query->whereDate('attendance_date', $this->filters['date']);
        }
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'done') {
                $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
            } elseif ($this->filters['status'] === 'ongoing') {
                $query->whereNull('checkout_time');
            } elseif ($this->filters['status'] === 'auto_checkout') {
                $query->where('is_auto_checkout', true);
            }
        }

        return $query->orderBy('attendance_date', 'desc')->orderBy('checkin_time', 'desc');
    }

    public function headings(): array
    {
        return ['No', 'ID Kehadiran', 'Tanggal', 'Sales', 'Toko', 'Nomor Part', 'Quantity', 'Catatan'];
    }

    public function map($att): array
    {
        $rows = [];
        foreach ($att->items as $item) {
            $this->rowNumber++;
            $rows[] = [
                $this->rowNumber,
                $att->id,
                $att->attendance_date->format('d/m/Y'),
                $att->user->name,
                $att->store_name,
                $item->part_number,
                $item->quantity,
                $item->notes ?? '—',
            ];
        }
        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 14,
            'C' => 14,
            'D' => 22,
            'E' => 28,
            'F' => 22,
            'G' => 12,
            'H' => 35,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->rowNumber + 1;

        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D61AF']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        if ($lastRow > 1) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $fillColor = ($row % 2 === 0) ? 'FFFAFBFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'font'      => ['size' => 10],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
            }

            // Center: No, ID Kehadiran, Tanggal, Quantity
            $sheet->getStyle("A2:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
            ]);
        } else {
            $sheet->setCellValue('A2', 'Tidak ada data part pada filter yang dipilih.');
            $sheet->getStyle('A2')->applyFromArray([
                'font' => ['color' => ['argb' => 'FF94A3B8'], 'italic' => true, 'size' => 10],
            ]);
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:H1");

        return [];
    }
}

// namespace App\Exports;

// use App\Models\Attendance;
// use Maatwebsite\Excel\Concerns\FromQuery;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithMultipleSheets;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

// class AttendancesExport implements WithMultipleSheets
// {
//     protected array $filters;

//     public function __construct(array $filters = [])
//     {
//         $this->filters = $filters;
//     }

//     public function sheets(): array
//     {
//         return [
//             new AttendancesMainSheet($this->filters),
//             new AttendancesItemsSheet($this->filters),
//         ];
//     }
// }

// // ─── Sheet 1: Data Absensi ────────────────────────────────────────────────────

// class AttendancesMainSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
// {
//     protected array $filters;
//     protected int $rowNumber = 0;
//     protected array $pendingHyperlinks = [];

//     public function __construct(array $filters = [])
//     {
//         $this->filters = $filters;
//     }

//     public function title(): string
//     {
//         return 'Data Absensi';
//     }

//     public function query()
//     {
//         return $this->buildQuery();
//     }

//     protected function buildQuery()
//     {
//         $query = Attendance::with(['user', 'items']);

//         if (!empty($this->filters['user_auth_id']) && !empty($this->filters['is_admin']) && !$this->filters['is_admin']) {
//             $query->where('user_id', $this->filters['user_auth_id']);
//         }
//         if (!empty($this->filters['search'])) {
//             $query->where(function ($q) {
//                 $q->where('store_name', 'like', '%' . $this->filters['search'] . '%')
//                   ->orWhere('person_in_charge_name', 'like', '%' . $this->filters['search'] . '%')
//                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->filters['search'] . '%'));
//             });
//         }
//         if (!empty($this->filters['user_id'])) {
//             $query->where('user_id', $this->filters['user_id']);
//         }
//         if (!empty($this->filters['date'])) {
//             $query->whereDate('attendance_date', $this->filters['date']);
//         }
//         if (!empty($this->filters['status'])) {
//             if ($this->filters['status'] === 'done') {
//                 $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
//             } elseif ($this->filters['status'] === 'ongoing') {
//                 $query->whereNull('checkout_time');
//             } elseif ($this->filters['status'] === 'auto_checkout') {
//                 $query->where('is_auto_checkout', true);
//             }
//         }

//         return $query->orderBy('attendance_date', 'desc')->orderBy('checkin_time', 'desc');
//     }

//     public function headings(): array
//     {
//         return [
//             'No', 'Sales', 'Toko', 'Nama PIC', 'No. Telepon PIC',
//             'Tanggal', 'Check-in', 'Lokasi Check-in', 'Foto Check-in',
//             'Check-out', 'Lokasi Check-out', 'Foto Check-out',
//             'Durasi', 'Status', 'Jumlah Part',
//         ];
//     }

//     public function map($att): array
//     {
//         $this->rowNumber++;
//         $currentRow = $this->rowNumber + 1;

//         if ($att->work_duration_minutes && !$att->is_auto_checkout) {
//             $hours  = intdiv($att->work_duration_minutes, 60);
//             $mins   = $att->work_duration_minutes % 60;
//             $durasi = ($hours > 0 ? $hours . ' jam ' : '') . $mins . ' menit';
//         } elseif (!$att->checkout_time) {
//             $durasi = 'Berlangsung';
//         } else {
//             $durasi = '—';
//         }

//         if ($att->is_auto_checkout) {
//             $status = 'Tidak Checkout';
//         } elseif ($att->checkout_time) {
//             $status = 'Selesai';
//         } else {
//             $status = 'Di Lapangan';
//         }

//         $checkinMapsUrl  = "https://www.google.com/maps?q={$att->checkin_latitude},{$att->checkin_longitude}";
//         $checkoutMapsUrl = $att->checkout_latitude
//             ? "https://www.google.com/maps?q={$att->checkout_latitude},{$att->checkout_longitude}"
//             : null;

//         $this->pendingHyperlinks[$currentRow] = [
//             'checkin_maps'   => ['col' => 'H', 'url' => $checkinMapsUrl],
//             'checkin_photo'  => ['col' => 'I', 'url' => $att->checkin_photo],
//             'checkout_maps'  => ['col' => 'K', 'url' => $checkoutMapsUrl],
//             'checkout_photo' => ['col' => 'L', 'url' => $att->checkout_photo ?? null],
//         ];

//         return [
//             $this->rowNumber,
//             $att->user->name,
//             $att->store_name,
//             $att->person_in_charge_name,
//             $att->person_in_charge_phone,
//             $att->attendance_date->format('d/m/Y'),
//             $att->checkin_time->format('H:i'),
//             'Lihat Lokasi ↗',
//             'Lihat Foto ↗',
//             $att->checkout_time ? $att->checkout_time->format('H:i') : '—',
//             $checkoutMapsUrl  ? 'Lihat Lokasi ↗' : '—',
//             $att->checkout_photo ? 'Lihat Foto ↗' : '—',
//             $durasi,
//             $status,
//             $att->items->count() > 0 ? $att->items->count() . ' part' : '—',
//         ];
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 5,  'B' => 22, 'C' => 28, 'D' => 22, 'E' => 20,
//             'F' => 14, 'G' => 12, 'H' => 18, 'I' => 16, 'J' => 12,
//             'K' => 18, 'L' => 16, 'M' => 16, 'N' => 16, 'O' => 14,
//         ];
//     }

//     public function styles(Worksheet $sheet)
//     {
//         $lastRow = $this->rowNumber + 1;

//         $sheet->getStyle('A1:O1')->applyFromArray([
//             'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D61AF']],
//             'alignment' => [
//                 'horizontal' => Alignment::HORIZONTAL_CENTER,
//                 'vertical'   => Alignment::VERTICAL_CENTER,
//                 'wrapText'   => true,
//             ],
//         ]);
//         $sheet->getRowDimension(1)->setRowHeight(30);

//         if ($lastRow > 1) {
//             for ($row = 2; $row <= $lastRow; $row++) {
//                 $fillColor = ($row % 2 === 0) ? 'FFFAFBFC' : 'FFFFFFFF';
//                 $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
//                     'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
//                     'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
//                     'font'      => ['size' => 10],
//                 ]);
//                 $sheet->getRowDimension($row)->setRowHeight(22);
//             }

//             $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//             $sheet->getStyle("F2:O{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

//             for ($row = 2; $row <= $lastRow; $row++) {
//                 $status = $sheet->getCell("N{$row}")->getValue();
//                 if ($status === 'Selesai') {
//                     $sheet->getStyle("N{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FF059669'], 'bold' => true]]);
//                 } elseif ($status === 'Di Lapangan') {
//                     $sheet->getStyle("N{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true]]);
//                 } elseif ($status === 'Tidak Checkout') {
//                     $sheet->getStyle("N{$row}")->applyFromArray(['font' => ['color' => ['argb' => 'FFEF4444'], 'bold' => true]]);
//                 }
//             }

//             foreach ($this->pendingHyperlinks as $row => $links) {
//                 foreach ($links as $link) {
//                     if (!$link['url']) continue;
//                     $cell = $sheet->getCell($link['col'] . $row);
//                     $cell->setHyperlink(new Hyperlink($link['url']));
//                     $sheet->getStyle($link['col'] . $row)->applyFromArray([
//                         'font' => ['color' => ['argb' => 'FF1D61AF'], 'underline' => true, 'size' => 10],
//                     ]);
//                 }
//             }

//             $sheet->getStyle("A1:O{$lastRow}")->applyFromArray([
//                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
//             ]);
//         }

//         $sheet->freezePane('A2');
//         $sheet->setAutoFilter("A1:O1");

//         return [];
//     }
// }

// // ─── Sheet 2: Daftar Part ─────────────────────────────────────────────────────

// class AttendancesItemsSheet implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
// {
//     protected array $filters;
//     protected int $rowNumber = 0;

//     public function __construct(array $filters = [])
//     {
//         $this->filters = $filters;
//     }

//     public function title(): string
//     {
//         return 'Daftar Part';
//     }

//     public function query()
//     {
//         $query = Attendance::with(['user', 'items'])
//             ->has('items'); // hanya attendance yang punya items

//         if (!empty($this->filters['user_auth_id']) && !empty($this->filters['is_admin']) && !$this->filters['is_admin']) {
//             $query->where('user_id', $this->filters['user_auth_id']);
//         }
//         if (!empty($this->filters['search'])) {
//             $query->where(function ($q) {
//                 $q->where('store_name', 'like', '%' . $this->filters['search'] . '%')
//                   ->orWhere('person_in_charge_name', 'like', '%' . $this->filters['search'] . '%')
//                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->filters['search'] . '%'));
//             });
//         }
//         if (!empty($this->filters['user_id'])) {
//             $query->where('user_id', $this->filters['user_id']);
//         }
//         if (!empty($this->filters['date'])) {
//             $query->whereDate('attendance_date', $this->filters['date']);
//         }
//         if (!empty($this->filters['status'])) {
//             if ($this->filters['status'] === 'done') {
//                 $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
//             } elseif ($this->filters['status'] === 'ongoing') {
//                 $query->whereNull('checkout_time');
//             } elseif ($this->filters['status'] === 'auto_checkout') {
//                 $query->where('is_auto_checkout', true);
//             }
//         }

//         return $query->orderBy('attendance_date', 'desc')->orderBy('checkin_time', 'desc');
//     }

//     public function headings(): array
//     {
//         return ['No', 'Tanggal', 'Sales', 'Toko', 'Nomor Part', 'Quantity', 'Catatan'];
//     }

//     public function map($att): array
//     {
//         $rows = [];
//         foreach ($att->items as $item) {
//             $this->rowNumber++;
//             $rows[] = [
//                 $this->rowNumber,
//                 $att->attendance_date->format('d/m/Y'),
//                 $att->user->name,
//                 $att->store_name,
//                 $item->part_number,
//                 $item->quantity,
//                 $item->notes ?? '—',
//             ];
//         }
//         return $rows;
//     }

//     public function columnWidths(): array
//     {
//         return [
//             'A' => 5,
//             'B' => 14,
//             'C' => 22,
//             'D' => 28,
//             'E' => 22,
//             'F' => 12,
//             'G' => 35,
//         ];
//     }

//     public function styles(Worksheet $sheet)
//     {
//         $lastRow = $this->rowNumber + 1;

//         $sheet->getStyle('A1:G1')->applyFromArray([
//             'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D61AF']],
//             'alignment' => [
//                 'horizontal' => Alignment::HORIZONTAL_CENTER,
//                 'vertical'   => Alignment::VERTICAL_CENTER,
//             ],
//         ]);
//         $sheet->getRowDimension(1)->setRowHeight(30);

//         if ($lastRow > 1) {
//             for ($row = 2; $row <= $lastRow; $row++) {
//                 $fillColor = ($row % 2 === 0) ? 'FFFAFBFC' : 'FFFFFFFF';
//                 $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
//                     'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $fillColor]],
//                     'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
//                     'font'      => ['size' => 10],
//                 ]);
//                 $sheet->getRowDimension($row)->setRowHeight(22);
//             }

//             $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//             $sheet->getStyle("B2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
//             $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

//             $sheet->getStyle("A1:G{$lastRow}")->applyFromArray([
//                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]],
//             ]);
//         } else {
//             // Kalau tidak ada data items
//             $sheet->setCellValue('A2', 'Tidak ada data part pada filter yang dipilih.');
//             $sheet->getStyle('A2')->applyFromArray([
//                 'font' => ['color' => ['argb' => 'FF94A3B8'], 'italic' => true, 'size' => 10],
//             ]);
//         }

//         $sheet->freezePane('A2');
//         $sheet->setAutoFilter("A1:G1");

//         return [];
//     }
// }
