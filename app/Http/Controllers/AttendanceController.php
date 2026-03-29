<?php

namespace App\Http\Controllers;
use App\Exports\AttendanceItemTemplateExport;
use App\Exports\AttendancesExport;
use App\Models\Attendance;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    use LogsActivity;
    
    public function index(Request $request)
    {
        $query = Attendance::with('user', 'items')
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

    public function downloadItemTemplate()
    {
        return Excel::download(
            new AttendanceItemTemplateExport(),
            'template-input-part.xlsx'
        );
    }

    // ── Detail ────────────────────────────────────────────────────────────
    public function show(Attendance $attendance)
    {
        // Sales hanya bisa lihat miliknya sendiri
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            abort(403);
        }

        $attendance->load(['user', 'items']);

        // Ambil semua log, kelompokkan berdasarkan created_at
        $logs = $attendance->activityLogs()
            ->with('user')
            ->get()
            ->groupBy(fn($log) => $log->created_at->format('Y-m-d H:i:s'))
            ->map(fn($group) => [
                'user'       => $group->first()->user,
                'created_at' => $group->first()->created_at,
                'changes'    => $group->map(fn($log) => [
                    'field_name' => $log->field_name,
                    'old_value'  => $log->oldValueDecoded(),
                    'new_value'  => $log->newValueDecoded(),
                    'is_items'   => $log->isItemsLog(),
                ])->values(),
            ])
            ->values();

        return view('attendances.show', compact('attendance', 'logs'));
    }

    // ── Edit Form ─────────────────────────────────────────────────────────
    public function edit(Attendance $attendance)
    {
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            abort(403);
        }

        $attendance->load(['user', 'items']);

        return view('attendances.edit', compact('attendance'));
    }

    // ── Update Attendance ─────────────────────────────────────────────────
    public function update(Request $request, Attendance $attendance)
    {
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'store_name'             => 'required|string|max:255',
            'person_in_charge_name'  => 'required|string|max:255',
            'person_in_charge_phone' => 'required|string|max:20',
        ], [
            'store_name.required'             => 'Nama toko wajib diisi.',
            'person_in_charge_name.required'  => 'Nama PIC wajib diisi.',
            'person_in_charge_phone.required' => 'Nomor telepon PIC wajib diisi.',
        ]);

        $watchedFields = ['store_name', 'person_in_charge_name', 'person_in_charge_phone'];

        $oldData = $attendance->only($watchedFields);
        $newData = $request->only($watchedFields);

        // Log perubahan
        $this->logChanges('attendance', $attendance->id, $oldData, $newData, $watchedFields);

        $attendance->update($newData);

        return redirect()
            ->route('attendances.show', $attendance)
            ->with('success', 'Data kunjungan berhasil diperbarui.');
    }

    // ── Update Items ──────────────────────────────────────────────────────
    public function updateItems(Request $request, Attendance $attendance)
    {
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.part_number' => 'required|string|max:100',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.notes'       => 'nullable|string|max:255',
        ], [
            'items.required'               => 'Minimal 1 item harus diisi.',
            'items.*.part_number.required' => 'Nomor part wajib diisi.',
            'items.*.quantity.required'    => 'Quantity wajib diisi.',
            'items.*.quantity.integer'     => 'Quantity harus berupa angka bulat.',
            'items.*.quantity.min'         => 'Quantity minimal 1.',
        ]);

        // Ambil items lama untuk log
        $oldItems = $attendance->items()
            ->get()
            ->map(fn($i) => [
                'part_number' => $i->part_number,
                'quantity'    => $i->quantity,
                'notes'       => $i->notes,
            ])
            ->toArray();

        // Gabungkan duplikat part_number
        $newItems = collect($request->items)
            ->groupBy('part_number')
            ->map(fn($group, $partNumber) => [
                'part_number' => $partNumber,
                'quantity'    => $group->sum('quantity'),
                'notes'       => $group->last()['notes'] ?? null,
            ])
            ->values()
            ->toArray();

        // Log perubahan items
        $this->logItemsChange($attendance->id, $oldItems, $newItems);

        // Replace items
        $attendance->items()->delete();
        $attendance->items()->createMany($newItems);

        return redirect()
            ->route('attendances.show', $attendance)
            ->with('success', 'Data items berhasil diperbarui.');
    }

    // ── Import Preview (AJAX) ─────────────────────────────────────────────────
    public function importItemsPreview(Request $request, Attendance $attendance)
    {
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes'    => 'File harus berformat xlsx atau xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new \App\Imports\AttendanceItemsImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));

        $validItems = $import->getValidItems();
        $warnings   = $import->getWarnings();

        if (empty($validItems)) {
            return response()->json([
                'success'  => false,
                'message'  => 'Tidak ada data valid yang dapat diimport.',
                'warnings' => $warnings,
            ], 422);
        }

        return response()->json([
            'success'  => true,
            'items'    => $validItems,
            'warnings' => $warnings,
            'total'    => count($validItems),
            'total_qty'=> array_sum(array_column($validItems, 'quantity')),
        ]);
    }

    // ── Import Confirm (AJAX) ─────────────────────────────────────────────────
    public function importItemsConfirm(Request $request, Attendance $attendance)
    {
        if (!auth()->user()->isAdmin() && $attendance->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.part_number' => 'required|string|max:100',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.notes'       => 'nullable|string|max:255',
        ]);

        // Ambil items lama untuk log
        $oldItems = $attendance->items()
            ->get()
            ->map(fn($i) => [
                'part_number' => $i->part_number,
                'quantity'    => $i->quantity,
                'notes'       => $i->notes,
            ])
            ->toArray();

        $newItems = $request->items;

        // Log perubahan
        $this->logItemsChange($attendance->id, $oldItems, $newItems);

        // Replace
        $attendance->items()->delete();
        $attendance->items()->createMany($newItems);

        return response()->json([
            'success' => true,
            'message' => 'Import berhasil. ' . count($newItems) . ' part tersimpan.',
            'redirect'=> route('attendances.show', $attendance),
        ]);
    }
}