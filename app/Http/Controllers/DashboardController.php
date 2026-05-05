<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->isAdmin();

        // ── Stat Cards ──
        $todayQuery = Attendance::whereDate('attendance_date', today());
        $monthQuery = Attendance::whereMonth('attendance_date', now()->month)
                                ->whereYear('attendance_date', now()->year);

        if (!$isAdmin) {
            $todayQuery->where('user_id', $user->id);
            $monthQuery->where('user_id', $user->id);
        }

        $todayVisits    = (clone $todayQuery)->count();
        $checkedOut     = (clone $todayQuery)->whereNotNull('checkout_time')->count();
        $stillOut       = (clone $todayQuery)->whereNull('checkout_time')->count();
        $totalSales     = $isAdmin ? User::where('role_id', function($q) {
                                $q->select('id')->from('roles')->where('name', 'Sales');
                            })->count()
                          : $monthQuery->count();

        // ── Chart: 7 hari terakhir ──
        $chartData = collect(range(6, 0))->map(function ($daysAgo) use ($isAdmin, $user) {
            $date = Carbon::today()->subDays($daysAgo);
            $q = Attendance::whereDate('attendance_date', $date);
            if (!$isAdmin) $q->where('user_id', $user->id);
            return [
                'label' => $date->locale('id')->isoFormat('ddd'),
                'date'  => $date->format('d/m'),
                'count' => $q->count(),
            ];
        });

        $chartMax = max($chartData->max('count'), 1);

        // ── Recent Attendance ──
        $recentAttendances = Attendance::with('user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->whereDate('attendance_date', today())
            ->latest('checkin_time')
            ->take(5)
            ->get();

        // ── Top Sales (admin only) ──
        $topSales = [];
        if ($isAdmin) {
            $topSales = User::withCount(['attendances as month_visits' => function ($q) {
                    $q->whereMonth('attendance_date', now()->month)
                      ->whereYear('attendance_date', now()->year);
                }])
                ->whereHas('role', fn($q) => $q->where('name', 'Sales'))
                ->orderByDesc('month_visits')
                ->take(5)
                ->get();

            $maxVisits = max($topSales->max('month_visits'), 1);
        }

        $maxVisits = $maxVisits ?? 1;

        // ── Recent Activity ──
        $recentActivity = Attendance::with('user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest('updated_at')
            ->take(5)
            ->get();
        
        // -─ Maps ──
        $mapAttendances = Attendance::with('user')
        ->whereNotNull('checkin_latitude')
        ->whereNotNull('checkin_longitude')
        ->whereDate('attendance_date', '>=', Carbon::now()->subDays(30))
        ->when(!$isAdmin, fn($q) => $q->where('user_id', auth()->id()))
        ->select(
            'id',
            'user_id',
            'store_name',
            'checkin_latitude',
            'checkin_longitude',
            'attendance_date',
            'checkin_time',
            'checkout_time'
        )
        ->latest('checkin_time')
        ->get()
        ->map(fn($att) => [
            'lat'          => (float) $att->checkin_latitude,
            'lng'          => (float) $att->checkin_longitude,
            'store_name'   => $att->store_name,
            'sales'        => $att->user?->name ?? 'User Dihapus',
            'date'         => $att->attendance_date->format('d M Y'),
            'checkintime'  => $att->checkin_time->format('H:i'),
            'checkouttime' => $att->checkout_time?->format('H:i') ?? 'Belum checkout',
        ]);
        // $mapAttendances = Attendance::with('user')
        //     ->whereNotNull('checkin_latitude')
        //     ->whereNotNull('checkin_longitude')
        //     ->when(!$isAdmin, fn($q) => $q->where('user_id', auth()->id()))
        //     ->select('id', 'user_id', 'store_name', 'checkin_latitude', 'checkin_longitude', 'attendance_date', 'checkin_time', 'checkout_time')
        //     ->latest('checkin_time')
        //     ->get()
        //     ->map(fn($att) => [
        //         'lat'        => (float) $att->checkin_latitude,
        //         'lng'        => (float) $att->checkin_longitude,
        //         'store_name' => $att->store_name,
        //         'sales'      => $att->user?->name ?? 'User Dihapus',
        //         'date'       => $att->attendance_date->format('d M Y'),
        //         'checkintime'       => $att->checkin_time->format('H:i'),
        //         'checkouttime'      => $att->checkout_time?->format('H:i') ?? 'Belum checkout',
        //     ]);

        return view('dashboard.index', compact(
            'isAdmin',
            'todayVisits', 'checkedOut', 'stillOut', 'totalSales',
            'chartData', 'chartMax',
            'recentAttendances',
            'topSales', 'maxVisits',
            'recentActivity',
            'mapAttendances'
        ));
    }
}