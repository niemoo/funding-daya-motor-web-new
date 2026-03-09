<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\User;

trait AttendanceStatusTrait
{
    protected function getAttendanceStatus(User $user): array
{
    // Cek apakah ada attendance ONGOING (belum checkout)
    $ongoingAttendance = Attendance::where('user_id', $user->id)
        ->whereNull('checkout_time')  // ← kunci utama: belum checkout
        ->latest()
        ->first();

    // Attendance terakhir hari ini (sudah selesai maupun ongoing)
    $lastAttendance = Attendance::where('user_id', $user->id)
        ->whereDate('attendance_date', today())
        ->latest()
        ->first();

    if ($ongoingAttendance) {
        // Ada sesi yang belum checkout — harus checkout dulu
        return [
            'can_checkin'      => false,
            'can_checkout'     => true,
            'is_checked_in'    => true,
            'is_checked_out'   => false,
            'last_checkin_at'  => optional($ongoingAttendance->checkin_time)->format('H:i'),
            'last_checkout_at' => null,
            'current_store'    => [
                'store_name'   => $ongoingAttendance->store_name,
                'checkin_time' => optional($ongoingAttendance->checkin_time)->format('H:i'),
            ],
            'message' => 'Sedang check-in di ' . $ongoingAttendance->store_name . '.',
        ];
    }

    if ($lastAttendance) {
        // Ada attendance hari ini tapi sudah selesai semua
        return [
            'can_checkin'      => true,
            'can_checkout'     => false,
            'is_checked_in'    => true,
            'is_checked_out'   => true,
            'last_checkin_at'  => optional($lastAttendance->checkin_time)->format('H:i'),
            'last_checkout_at' => optional($lastAttendance->checkout_time)->format('H:i'),
            'current_store'    => null,
            'message'          => 'Kunjungan terakhir di ' . $lastAttendance->store_name .
                                   ' selesai pukul ' .
                                   optional($lastAttendance->checkout_time)->format('H:i') .
                                   '. Siap untuk kunjungan berikutnya.',
        ];
    }

    // Belum ada attendance hari ini sama sekali
    return [
        'can_checkin'      => true,
        'can_checkout'     => false,
        'is_checked_in'    => false,
        'is_checked_out'   => false,
        'last_checkin_at'  => null,
        'last_checkout_at' => null,
        'current_store'    => null,
        'message'          => 'Belum ada kunjungan hari ini.',
    ];
}
    // private function getAttendanceStatus(?Attendance $attendance): array
    // {
    //     if (!$attendance) {
    //         return [
    //             'can_checkin'      => true,
    //             'can_checkout'     => false,
    //             'is_checked_in'    => false,
    //             'is_checked_out'   => false,
    //             'last_checkin_at'  => null,
    //             'last_checkout_at' => null,
    //             'current_store'    => null,
    //             'message'          => 'Belum melakukan check-in hari ini.',
    //         ];
    //     }

    //     if (!$attendance->checkout_time) {
    //         return [
    //             'can_checkin'      => false,
    //             'can_checkout'     => true,
    //             'is_checked_in'    => true,
    //             'is_checked_out'   => false,
    //             'last_checkin_at'  => $attendance->checkin_time->format('H:i'),
    //             'last_checkout_at' => null,
    //             'current_store'    => [
    //                 'attendance_id' => $attendance->id,
    //                 'store_name'    => $attendance->store_name,
    //                 'checkin_time'  => $attendance->checkin_time->format('H:i'),
    //             ],
    //             'message' => 'Sedang check-in di ' . $attendance->store_name . '. Silakan checkout terlebih dahulu.',
    //         ];
    //     }

    //     return [
    //         'can_checkin'      => true,
    //         'can_checkout'     => false,
    //         'is_checked_in'    => true,
    //         'is_checked_out'   => true,
    //         'last_checkin_at'  => $attendance->checkin_time->format('H:i'),
    //         'last_checkout_at' => $attendance->checkout_time->format('H:i'),
    //         'current_store'    => null,
    //         'message'          => 'Kunjungan terakhir di ' . $attendance->store_name
    //                             . ' selesai pukul ' . $attendance->checkout_time->format('H:i')
    //                             . '. Siap untuk kunjungan berikutnya.',
    //     ];
    // }
}