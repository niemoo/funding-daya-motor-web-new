<?php

namespace App\Traits;

use App\Models\Attendance;

trait AttendanceStatusTrait
{
    private function getAttendanceStatus(?Attendance $attendance): array
    {
        if (!$attendance) {
            return [
                'can_checkin'      => true,
                'can_checkout'     => false,
                'is_checked_in'    => false,
                'is_checked_out'   => false,
                'last_checkin_at'  => null,
                'last_checkout_at' => null,
                'current_store'    => null,
                'message'          => 'Belum melakukan check-in hari ini.',
            ];
        }

        if (!$attendance->checkout_time) {
            return [
                'can_checkin'      => false,
                'can_checkout'     => true,
                'is_checked_in'    => true,
                'is_checked_out'   => false,
                'last_checkin_at'  => $attendance->checkin_time->format('H:i'),
                'last_checkout_at' => null,
                'current_store'    => [
                    'attendance_id' => $attendance->id,
                    'store_name'    => $attendance->store_name,
                    'checkin_time'  => $attendance->checkin_time->format('H:i'),
                ],
                'message' => 'Sedang check-in di ' . $attendance->store_name . '. Silakan checkout terlebih dahulu.',
            ];
        }

        return [
            'can_checkin'      => true,
            'can_checkout'     => false,
            'is_checked_in'    => true,
            'is_checked_out'   => true,
            'last_checkin_at'  => $attendance->checkin_time->format('H:i'),
            'last_checkout_at' => $attendance->checkout_time->format('H:i'),
            'current_store'    => null,
            'message'          => 'Kunjungan terakhir di ' . $attendance->store_name
                                . ' selesai pukul ' . $attendance->checkout_time->format('H:i')
                                . '. Siap untuk kunjungan berikutnya.',
        ];
    }
}