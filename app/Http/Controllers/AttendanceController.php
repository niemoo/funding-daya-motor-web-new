<?php

namespace App\Http\Controllers;
use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user')
            ->when(!auth()->user()->isAdmin(), fn($q) => $q->where('user_id', auth()->id()));

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('store_name', 'like', '%' . $request->search . '%')
                  ->orWhere('person_in_charge_name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        // Filter by sales (admin only)
        if ($request->filled('user_id') && auth()->user()->isAdmin()) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'done') {
                $query->whereNotNull('checkout_time')->where('is_auto_checkout', false);
            } elseif ($request->status === 'ongoing') {
                $query->whereNull('checkout_time');
            } elseif ($request->status === 'auto_checkout') {
                $query->where('is_auto_checkout', true);
            }
        }

        // Sort
        $sortable = ['attendance_date', 'checkin_time', 'store_name', 'work_duration_minutes'];
        $sort = in_array($request->sort, $sortable) ? $request->sort : 'checkin_time';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $dir);

        $attendances = $query->paginate(10)->withQueryString();

        // Untuk filter dropdown sales (admin only)
        $salesList = auth()->user()->isAdmin()
            ? User::whereHas('role', fn($q) => $q->where('name', 'Sales'))->orderBy('name')->get()
            : collect();

        return view('attendances.index', compact('attendances', 'salesList', 'sort', 'dir'));
    }

    public function export(Request $request)
    {
        $filters = $request->all();
        $filters['is_admin']     = auth()->user()->isAdmin();
        $filters['user_auth_id'] = auth()->id();

        $filename = 'absensi-' . now()->format('d-m-Y') . '.xlsx';

        return Excel::download(new AttendancesExport($filters), $filename);
    }
}