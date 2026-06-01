<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attendances = Attendance::whereNotNull('checkout_time')->get();

        $sampleParts = [
            ['kode_part' => 'BP-001', 'quantity' => 5, 'notes' => null],
            ['kode_part' => 'BP-002', 'quantity' => 3, 'notes' => 'Urgent'],
            ['kode_part' => 'SK-101', 'quantity' => 10, 'notes' => null],
            ['kode_part' => 'SK-102', 'quantity' => 2, 'notes' => 'Cek stok dulu'],
            ['kode_part' => 'FR-201', 'quantity' => 7, 'notes' => null],
            ['kode_part' => 'FR-202', 'quantity' => 4, 'notes' => 'Request tambahan'],
            ['kode_part' => 'OL-301', 'quantity' => 6, 'notes' => null],
            ['kode_part' => 'OL-302', 'quantity' => 1, 'notes' => 'Sample'],
        ];

        foreach ($attendances as $attendance) {
            // Ambil 2-4 part random per attendance
            $parts = collect($sampleParts)->random(rand(2, 4));

            foreach ($parts as $part) {
                AttendanceItem::create([
                    'attendance_id' => $attendance->id,
                    'kode_part'   => $part['kode_part'],
                    'quantity'      => $part['quantity'],
                    'notes'         => $part['notes'],
                ]);
            }
        }
    }
}
