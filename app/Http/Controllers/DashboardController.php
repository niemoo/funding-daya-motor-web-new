<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceItem;
use App\Models\Part;
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
        $totalSales = $isAdmin
                    ? User::role('Sales')->count()
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
                        ->role('Sales')
                        ->orderByDesc('month_visits')
                        ->take(5)
                        ->get();
        }

        $maxVisits = $maxVisits ?? 1;

        // ── Recent Activity ──
        $recentActivity = Attendance::with('user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->latest('updated_at')
            ->take(5)
            ->get();

        // ── Top 10 Parts Terlaris ──
        $topPartsRaw = AttendanceItem::query()
            ->selectRaw('kode_part, SUM(quantity) as total_qty')
            ->whereHas('attendance', fn($q) => $q->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year))
            ->groupBy('kode_part')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        // Ambil total supply per kode_part
        $supplyTotals = \App\Models\AttendanceSupply::query()
            ->selectRaw('kode_part, SUM(quantity_supplied) as total_supplied')
            ->whereHas('attendance', fn($q) => $q->whereMonth('attendance_date', now()->month)
                ->whereYear('attendance_date', now()->year))
            ->whereIn('kode_part', $topPartsRaw->pluck('kode_part'))
            ->groupBy('kode_part')
            ->get()
            ->keyBy('kode_part');

        $topParts = $topPartsRaw->map(function ($item) use ($supplyTotals) {
            $part = Part::withTrashed()->where('kode_part', $item->kode_part)->first();
            $totalSupplied = $supplyTotals[$item->kode_part]->total_supplied ?? 0;
            return [
                'kode_part'      => $item->kode_part,
                'deskripsi_part' => $part?->deskripsi_part ?? '—',
                'total_qty'      => (int)$item->total_qty,
                'total_supplied' => (int)$totalSupplied,
                'het'            => $part?->het ?? 0,
                'total_nilai'    => $item->total_qty * ($part?->het ?? 0),
            ];
        });

        // ── Top 5 Toko Terlaris ──
        $topStores = Attendance::query()
            ->selectRaw('general_store_id, COUNT(*) as total_visits,
                SUM((SELECT SUM(quantity) FROM attendance_items WHERE attendance_id = attendances.id)) as total_qty,
                SUM((SELECT SUM(ai.quantity * p.het) FROM attendance_items ai LEFT JOIN parts p ON p.kode_part = ai.kode_part WHERE ai.attendance_id = attendances.id)) as total_nilai')
            ->whereNotNull('general_store_id')
            ->whereMonth('attendance_date', now()->month)
            ->whereYear('attendance_date', now()->year)
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $user->id))
            ->groupBy('general_store_id')
            ->orderByDesc('total_nilai')
            ->limit(5)
            ->with('generalStore')
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

        return view('dashboard.index', compact(
            'isAdmin',
            'todayVisits', 'checkedOut', 'stillOut', 'totalSales',
            'chartData', 'chartMax',
            'recentAttendances',
            'topSales', 'maxVisits',
            'recentActivity',
            'mapAttendances',
            'topParts',
            'topStores',
        ));
    }
}