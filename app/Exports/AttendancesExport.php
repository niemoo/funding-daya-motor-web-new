<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;

class AttendancesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected array $filters;
    protected int $rowNumber = 0;
    protected Worksheet $sheet;

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
        $query = Attendance::with('user');

        // Filter by role (sales hanya lihat data sendiri)
        if (!empty($this->filters['user_auth_id']) && !empty($this->filters['is_admin']) && !$this->filters['is_admin']) {
            $query->where('user_id', $this->filters['user_auth_id']);
        }

        // Filter search
        if (!empty($this->filters['search'])) {
            $query->where(function ($q) {
                $q->where('store_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('person_in_charge_name', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->filters['search'] . '%'));
            });
        }

        // Filter by sales
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        // Filter by date
        if (!empty($this->filters['date'])) {
            $query->whereDate('attendance_date', $this->filters['date']);
        }

        // Filter by status
        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'done') {
                $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
            } elseif ($this->filters['status'] === 'ongoing') {
                $query->whereNull('checkout_time');
            } elseif ($this->filters['status'] === 'auto_checkout') {
                $query->where('is_auto_checkout', true);
            }
        }
        // if (!empty($this->filters['status'])) {
        //     if ($this->filters['status'] === 'done') {
        //         $query->whereNotNull('checkout_time');
        //     } elseif ($this->filters['status'] === 'ongoing') {
        //         $query->whereNull('checkout_time');
        //     }
        // }

        return $query->orderBy('attendance_date', 'desc')->orderBy('checkin_time', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Sales',
            'Toko',
            'Nama PIC',
            'No. Telepon PIC',
            'Tanggal',
            'Check-in',
            'Lokasi Check-in',
            'Foto Check-in',
            'Check-out',
            'Lokasi Check-out',
            'Foto Check-out',
            'Durasi',
            'Status',
        ];
    }

    public function map($att): array
    {
        $this->rowNumber++;
        $currentRow = $this->rowNumber + 1; // +1 karena baris 1 adalah header

        // Durasi
        if ($att->work_duration_minutes && !$att->is_auto_checkout) {
            $hours   = intdiv($att->work_duration_minutes, 60);
            $minutes = $att->work_duration_minutes % 60;
            $durasi  = ($hours > 0 ? $hours . ' jam ' : '') . $minutes . ' menit';
        } elseif (!$att->checkout_time) {
            $durasi = 'Berlangsung';
        } else {
            $durasi = '—';
        }

        // Status
        if ($att->is_auto_checkout) {
            $status = 'Tidak Checkout';
        } elseif ($att->checkout_time) {
            $status = 'Selesai';
        } else {
            $status = 'Di Lapangan';
        }

        // if ($att->work_duration_minutes) {
        //     $hours   = intdiv($att->work_duration_minutes, 60);
        //     $minutes = $att->work_duration_minutes % 60;
        //     $durasi  = ($hours > 0 ? $hours . ' jam ' : '') . $minutes . ' menit';
        // } else {
        //     $durasi = 'Berlangsung';
        // }

        // URLs
        $checkinMapsUrl  = "https://www.google.com/maps?q={$att->checkin_latitude},{$att->checkin_longitude}";
        $checkinPhotoUrl = $att->checkin_photo;

        $checkoutMapsUrl  = $att->checkout_latitude
            ? "https://www.google.com/maps?q={$att->checkout_latitude},{$att->checkout_longitude}"
            : null;
        $checkoutPhotoUrl = $att->checkout_photo ?? null;

        // Simpan info hyperlink untuk diproses di styles()
        $this->pendingHyperlinks[$currentRow] = [
            'checkin_maps'    => ['col' => 'H', 'url' => $checkinMapsUrl],
            'checkin_photo'   => ['col' => 'I', 'url' => $checkinPhotoUrl],
            'checkout_maps'   => ['col' => 'K', 'url' => $checkoutMapsUrl],
            'checkout_photo'  => ['col' => 'L', 'url' => $checkoutPhotoUrl],
        ];

        return [
            $this->rowNumber,
            $att->user->name,
            $att->store_name,
            $att->person_in_charge_name,
            $att->person_in_charge_phone,
            $att->attendance_date->format('d/m/Y'),
            $att->checkin_time->format('H:i'),
            'Lihat Lokasi ↗',
            'Lihat Foto ↗',
            $att->checkout_time ? $att->checkout_time->format('H:i') : '—',
            $checkoutMapsUrl  ? 'Lihat Lokasi ↗' : '—',
            $checkoutPhotoUrl ? 'Lihat Foto ↗'   : '—',
            $durasi,
            $status,
            // $att->checkout_time ? 'Selesai' : 'Di Lapangan',
        ];
    }

    protected array $pendingHyperlinks = [];

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 22,
            'C' => 28,
            'D' => 22,
            'E' => 20,
            'F' => 14,
            'G' => 12,
            'H' => 18,
            'I' => 16,
            'J' => 12,
            'K' => 18,
            'L' => 16,
            'M' => 16,
            'N' => 16,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $this->sheet = $sheet;
        $lastRow     = $this->rowNumber + 1;

        // ── Header row styling ──
        $sheet->getStyle('A1:N1')->applyFromArray([
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
                'wrapText'   => true,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Data rows styling ──
        if ($lastRow > 1) {
            // Zebra striping
            for ($row = 2; $row <= $lastRow; $row++) {
                $fillColor = ($row % 2 === 0) ? 'FFFAFBFC' : 'FFFFFFFF';
                $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => $fillColor],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => ['size' => 10],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(22);
            }

            // Center align certain columns
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F2:N{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Status column coloring
            for ($row = 2; $row <= $lastRow; $row++) {
                $status = $sheet->getCell("N{$row}")->getValue();
                if ($status === 'Selesai') {
                    $sheet->getStyle("N{$row}")->applyFromArray([
                        'font' => ['color' => ['argb' => 'FF059669'], 'bold' => true],
                    ]);
                } elseif ($status === 'Di Lapangan') {
                    $sheet->getStyle("N{$row}")->applyFromArray([
                        'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                    ]);
                } elseif ($status === 'Tidak Checkout') {
                    $sheet->getStyle("N{$row}")->applyFromArray([
                        'font' => ['color' => ['argb' => 'FFEF4444'], 'bold' => true],
                    ]);
                }
                // if ($status === 'Selesai') {
                //     $sheet->getStyle("N{$row}")->applyFromArray([
                //         'font' => ['color' => ['argb' => 'FF059669'], 'bold' => true],
                //     ]);
                // } elseif ($status === 'Di Lapangan') {
                //     $sheet->getStyle("N{$row}")->applyFromArray([
                //         'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                //     ]);
                // }
            }

            // ── Hyperlinks ──
            foreach ($this->pendingHyperlinks as $row => $links) {
                foreach ($links as $key => $link) {
                    if (!$link['url']) continue;

                    $cell = $sheet->getCell($link['col'] . $row);
                    $cell->setHyperlink(new Hyperlink($link['url']));
                    $sheet->getStyle($link['col'] . $row)->applyFromArray([
                        'font' => [
                            'color'     => ['argb' => 'FF1D61AF'],
                            'underline' => true,
                            'size'      => 10,
                        ],
                    ]);
                }
            }
        }

        // ── Border seluruh tabel ──
        if ($lastRow > 1) {
            $sheet->getStyle("A1:N{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ]);
        }

        // ── Freeze header row ──
        $sheet->freezePane('A2');

        // ── Auto filter ──
        $sheet->setAutoFilter("A1:N1");

        return [];
    }
}