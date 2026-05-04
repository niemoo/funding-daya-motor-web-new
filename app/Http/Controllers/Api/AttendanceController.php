<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    // ── Check-in ──
    public function checkin(Request $request)
    {
        $request->validate([
            'checkin_latitude'  => 'required|numeric',
            'checkin_longitude' => 'required|numeric',
            'checkin_time'      => 'required|date_format:Y-m-d H:i:s',
            'checkin_photo'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'store_name'        => 'required|string|max:255',
            'pic_name'          => 'required|string|max:255',
            'pic_phone'         => 'required|string|max:20',
        ], [
            'checkin_latitude.required'  => 'Latitude wajib diisi.',
            'checkin_latitude.numeric'   => 'Latitude harus berupa angka.',
            'checkin_longitude.required' => 'Longitude wajib diisi.',
            'checkin_longitude.numeric'  => 'Longitude harus berupa angka.',
            'checkin_time.required'      => 'Waktu check-in wajib diisi.',
            'checkin_time.date_format'   => 'Format waktu harus Y-m-d H:i:s.',
            'checkin_photo.required'     => 'Foto check-in wajib diupload.',
            'checkin_photo.image'        => 'File harus berupa gambar.',
            'checkin_photo.mimes'        => 'Format foto harus jpg, jpeg, png, atau webp.',
            'checkin_photo.max'          => 'Ukuran foto maksimal 5MB.',
            'store_name.required'        => 'Nama toko wajib diisi.',
            'pic_name.required'          => 'Nama PIC wajib diisi.',
            'pic_phone.required'         => 'Nomor telepon PIC wajib diisi.',
        ]);

        $user = $request->user();

        // Cek apakah masih ada kunjungan yang belum checkout
        $ongoingVisit = Attendance::where('user_id', $user->id)
            ->whereNull('checkout_time')
            ->latest('checkin_time')
            ->first();

        if ($ongoingVisit) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih check-in di ' . $ongoingVisit->store_name . '. Silakan checkout terlebih dahulu.',
                'data'    => [
                    'ongoing_attendance_id' => $ongoingVisit->id,
                    'store_name'            => $ongoingVisit->store_name,
                    'checkin_time'          => $ongoingVisit->checkin_time->format('H:i'),
                ],
            ], 422);
        }

        // Simpan foto
        $checkinTime = Carbon::parse($request->checkin_time);
        $photoPath   = $request->file('checkin_photo')->store(
            'photos/checkin/' . $checkinTime->format('Y/m'),
            'public'
        );

        // Buat attendance
        $attendance = Attendance::create([
            'user_id'                => $user->id,
            'attendance_date'        => $checkinTime->toDateString(),
            'checkin_time'           => $checkinTime,
            'checkin_latitude'       => $request->checkin_latitude,
            'checkin_longitude'      => $request->checkin_longitude,
            'checkin_photo'          => $photoPath,
            'store_name'             => $request->store_name,
            'person_in_charge_name'  => $request->pic_name,
            'person_in_charge_phone' => $request->pic_phone,
        ]);

        // $attendanceStatus = $this->getAttendanceStatus($user);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil di ' . $attendance->store_name . '.',
            'data'    => [
                'attendance_id'     => $attendance->id,
                'store_name'        => $attendance->store_name,
                'checkin_time'      => $attendance->checkin_time->format('H:i'),
                'checkin_photo_url' => Storage::url($photoPath),
            ],
        ], 201);
    }

    // ── Check-out ──
    public function checkout(Request $request)
    {
        $request->validate([
            'checkout_latitude'  => 'required|numeric',
            'checkout_longitude' => 'required|numeric',
            'checkout_time'      => 'required|date_format:Y-m-d H:i:s',
            'checkout_photo'     => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'payment_method'    => 'nullable|string|max:255',
        ], [
            'checkout_latitude.required'  => 'Latitude wajib diisi.',
            'checkout_latitude.numeric'   => 'Latitude harus berupa angka.',
            'checkout_longitude.required' => 'Longitude wajib diisi.',
            'checkout_longitude.numeric'  => 'Longitude harus berupa angka.',
            'checkout_time.required'      => 'Waktu check-out wajib diisi.',
            'checkout_time.date_format'   => 'Format waktu harus Y-m-d H:i:s.',
            'checkout_photo.required'     => 'Foto check-out wajib diupload.',
            'checkout_photo.image'        => 'File harus berupa gambar.',
            'checkout_photo.mimes'        => 'Format foto harus jpg, jpeg, png, atau webp.',
            'checkout_photo.max'          => 'Ukuran foto maksimal 5MB.',
            'payment_method.string'     => 'Jenis pembayaran harus berupa teks.',
            'payment_method.max'          => 'Jenis pembayaran maksimal 255 karakter.',
        ]);

        $user = $request->user();

        // Cari kunjungan yang sedang aktif
        $attendance = Attendance::where('user_id', $user->id)
            ->whereNull('checkout_time')
            ->latest('checkin_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kunjungan aktif. Silakan check-in terlebih dahulu.',
            ], 422);
        }

        // Simpan foto checkout
        $checkoutTime = Carbon::parse($request->checkout_time);
        $photoPath    = $request->file('checkout_photo')->store(
            'photos/checkout/' . $checkoutTime->format('Y/m'),
            'public'
        );

        // Hitung durasi dalam menit
        $durationMinutes = (int) $attendance->checkin_time->diffInMinutes($checkoutTime);

        // Update attendance
        $attendance->update([
            'checkout_time'      => $checkoutTime,
            'checkout_latitude'  => $request->checkout_latitude,
            'checkout_longitude' => $request->checkout_longitude,
            'checkout_photo'     => $photoPath,
            'work_duration_minutes' => $durationMinutes,
            'jenis_pembayaran'   => $request->payment_method,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil dari ' . $attendance->store_name . '.',
            'data'    => [
                'attendance_id'       => $attendance->id,
                'store_name'          => $attendance->store_name,
                'checkin_time'        => $attendance->checkin_time->format('H:i'),
                'checkout_time'       => $checkoutTime->format('H:i'),
                'work_duration_minutes' => $durationMinutes,
                'work_duration_label' => $this->formatDuration($durationMinutes),
                'checkout_photo_url'  => Storage::url($photoPath),
            ],
        ]);
    }

    // ── History Harian ──
    public function dailyHistory(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $user = $request->user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // ── Auto-checkout ongoing dari hari sebelumnya ────────────────────────
        // Hanya berlaku kalau $date > attendance_date ongoing tersebut
        $ongoingPastDay = Attendance::where('user_id', $user->id)
            ->whereNull('checkout_time')
            ->whereDate('attendance_date', '<', $date)
            ->latest('checkin_time')
            ->first();

        if ($ongoingPastDay) {
            $autoCheckoutTime = Carbon::parse($ongoingPastDay->attendance_date)
                ->setTime(23, 59, 59);

            $ongoingPastDay->update([
                'checkout_time'         => $autoCheckoutTime,
                'checkout_latitude'     => null,
                'checkout_longitude'    => null,
                'checkout_photo'        => null,
                'work_duration_minutes' => (int) $ongoingPastDay->checkin_time
                                            ->diffInMinutes($autoCheckoutTime),
                'is_auto_checkout'      => true,
            ]);
        }

        // ── Ambil attendances sesuai date yang di-request ─────────────────────
        $attendances = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $date)
            ->orderByDesc('id')
            ->get();

        // ── Cek ongoing (setelah auto-checkout, pasti hanya hari ini atau null) 
        $ongoingAttendance = Attendance::where('user_id', $user->id)
            ->whereNull('checkout_time')
            ->latest('checkin_time')
            ->first();

        $canCheckIn = $ongoingAttendance === null;

        $data = $attendances->map(function ($att) {
            return [
                'attendance_id'          => $att->id,
                'store_name'             => $att->store_name,
                'person_in_charge_name'  => $att->person_in_charge_name,
                'person_in_charge_phone' => $att->person_in_charge_phone,
                'checkin_time'           => $att->checkin_time->format('H:i'),
                'checkin_time_full'      => $att->checkin_time->toIso8601String(),
                'checkout_time'          => $att->checkout_time?->format('H:i'),
                'checkout_time_full'     => $att->checkout_time?->toIso8601String(),
                'work_duration_minutes'  => $att->work_duration_minutes,
                'work_duration_label'    => $att->work_duration_minutes
                    ? $this->formatDuration($att->work_duration_minutes)
                    : null,
                'status'                 => $att->checkout_time ? 'done' : 'ongoing',
                'checkin_photo_url'      => Storage::url($att->checkin_photo),
                'checkout_photo_url'     => $att->checkout_photo
                    ? Storage::url($att->checkout_photo)
                    : null,
                'checkin_latitude'       => $att->checkin_latitude,
                'checkin_longitude'      => $att->checkin_longitude,
                'checkout_latitude'      => $att->checkout_latitude,
                'checkout_longitude'     => $att->checkout_longitude,
                'is_auto_checkout'       => $att->is_auto_checkout,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'can_checkin'     => $canCheckIn,
                'ongoing'          => $ongoingAttendance ? [
                    'attendance_id'     => $ongoingAttendance->id,
                    'store_name'        => $ongoingAttendance->store_name,
                    'checkin_time_full' => $ongoingAttendance->checkin_time->toIso8601String(),
                ] : null,
                'date'             => $date->format('Y-m-d'),
                'date_label'       => $date->locale('id')->isoFormat('dddd, D MMMM Y'),
                'total_visits'     => $attendances->count(),
                'completed_visits' => $attendances->whereNotNull('checkout_time')->count(),
                'ongoing_visits'   => $attendances->whereNull('checkout_time')->count(),
                'attendances'      => $data,
            ],
        ]);
    }

    // ── Statistik: Hari ini & 7 Hari ──
    public function statistics(Request $request)
    {
        $request->validate([
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        $user = $request->user();
        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : Carbon::today();

        // ── Hari ini (berdasarkan $date) ──────────────────────────────────────
        $todayAttendances = Attendance::where('user_id', $user->id)
            ->whereDate('attendance_date', $date)
            ->get();

        // ── 7 Hari Terakhir dari $date ────────────────────────────────────────
        $weeklyData = collect(range(6, 0))->map(function ($daysAgo) use ($user, $date) {
            $day   = $date->copy()->subDays($daysAgo);
            $count = Attendance::where('user_id', $user->id)
                ->whereDate('attendance_date', $day)
                ->count();

            return [
                'date'       => $day->format('Y-m-d'),
                'date_label' => $day->locale('id')->isoFormat('ddd, D MMM'),
                'day_short'  => $day->locale('id')->isoFormat('ddd'),
                'visits'     => $count,
                'is_today'   => $day->toDateString() === $date->toDateString(),
            ];
        });

        $weeklyTotal = $weeklyData->sum('visits');

        return response()->json([
            'success' => true,
            'data'    => [
                'today' => [
                    'date'         => $date->format('Y-m-d'),
                    'date_label'   => $date->locale('id')->isoFormat('dddd, D MMMM Y'),
                    'total_visits' => $todayAttendances->count(),
                    'completed'    => $todayAttendances->whereNotNull('checkout_time')->count(),
                    'ongoing'      => $todayAttendances->whereNull('checkout_time')->count(),
                ],
                'weekly' => [
                    'total_visits'    => $weeklyTotal,
                    'average_per_day' => $weeklyData->where('visits', '>', 0)->avg('visits')
                        ? round($weeklyData->where('visits', '>', 0)->avg('visits'), 1)
                        : 0,
                    'days'            => $weeklyData->values(),
                ],
            ],
        ]);
    }

    // ── Helper: format durasi ──
    private function formatDuration(int $minutes): string
    {
        $hours   = intdiv($minutes, 60);
        $mins    = $minutes % 60;
        if ($hours > 0) {
            return $hours . ' jam ' . $mins . ' menit';
        }
        return $mins . ' menit';
    }
}