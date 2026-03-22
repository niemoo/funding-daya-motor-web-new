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
            ['part_number' => 'BP-001', 'quantity' => 5, 'notes' => null],
            ['part_number' => 'BP-002', 'quantity' => 3, 'notes' => 'Urgent'],
            ['part_number' => 'SK-101', 'quantity' => 10, 'notes' => null],
            ['part_number' => 'SK-102', 'quantity' => 2, 'notes' => 'Cek stok dulu'],
            ['part_number' => 'FR-201', 'quantity' => 7, 'notes' => null],
            ['part_number' => 'FR-202', 'quantity' => 4, 'notes' => 'Request tambahan'],
            ['part_number' => 'OL-301', 'quantity' => 6, 'notes' => null],
            ['part_number' => 'OL-302', 'quantity' => 1, 'notes' => 'Sample'],
        ];

        foreach ($attendances as $attendance) {
            // Ambil 2-4 part random per attendance
            $parts = collect($sampleParts)->random(rand(2, 4));

            foreach ($parts as $part) {
                AttendanceItem::create([
                    'attendance_id' => $attendance->id,
                    'part_number'   => $part['part_number'],
                    'quantity'      => $part['quantity'],
                    'notes'         => $part['notes'],
                ]);
            }
        }
    }
}
