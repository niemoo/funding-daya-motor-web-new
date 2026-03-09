<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AttendanceStatusTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use AttendanceStatusTrait;

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('role')
            ->where('email', $request->email)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if ($user->role->name !== 'Sales') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Sales yang dapat login di aplikasi mobile.',
            ], 403);
        }

        // Flagging status
        $attendanceStatus = $this->getAttendanceStatus($user);

        // Hapus token lama, buat token baru
        // $user->tokens()->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data'    => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role->name,
                ],
                'token'             => $token,
                'attendance_status' => $attendanceStatus,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('role');

        $attendanceStatus = $this->getAttendanceStatus($user);

        return response()->json([
            'success' => true,
            'data'    => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role->name,
                ],
                'attendance_status' => $attendanceStatus,
            ],
        ]);
    }

    // ── Helper: tentukan status absensi ──
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

    //     // Sudah checkin tapi belum checkout
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
    //             'message'          => 'Sedang check-in di ' . $attendance->store_name . '. Silakan checkout terlebih dahulu.',
    //         ];
    //     }

    //     // Sudah checkin dan sudah checkout — bisa checkin lagi
    //     return [
    //         'can_checkin'      => true,
    //         'can_checkout'     => false,
    //         'is_checked_in'    => true,
    //         'is_checked_out'   => true,
    //         'last_checkin_at'  => $attendance->checkin_time->format('H:i'),
    //         'last_checkout_at' => $attendance->checkout_time->format('H:i'),
    //         'current_store'    => null,
    //         'message'          => 'Kunjungan terakhir di ' . $attendance->store_name . ' selesai pukul ' . $attendance->checkout_time->format('H:i') . '. Siap untuk kunjungan berikutnya.',
    //     ];
    // }

    public function attendanceStatus(Request $request)
    {
        $user = $request->user();
   
        return response()->json([
            'success' => true,
            'data'    => $this->getAttendanceStatus($user),
        ]);
    }
}