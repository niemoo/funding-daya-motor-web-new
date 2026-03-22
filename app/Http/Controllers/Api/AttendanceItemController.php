<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\AttendanceItemsImport;
use App\Models\Attendance;
use App\Exports\AttendanceItemTemplateExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceItemController extends Controller
{
    // ── GET /api/attendances/{attendance}/items ────────────────────────────
    public function index(Attendance $attendance)
    {
        // Pastikan attendance milik user yang login
        if ($attendance->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $items = $attendance->items()->orderBy('id')->get()->map(fn($item) => [
            'id'          => $item->id,
            'part_number' => $item->part_number,
            'quantity'    => $item->quantity,
            'notes'       => $item->notes,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'attendance_id' => $attendance->id,
                'store_name'    => $attendance->store_name,
                'total_items'   => $items->count(),
                'total_qty'     => $items->sum('quantity'),
                'items'         => $items,
            ],
        ]);
    }

    // ── POST /api/attendances/{attendance}/items ───────────────────────────
    public function store(Request $request, Attendance $attendance)
    {
        if ($attendance->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $request->validate([
            'items'               => 'required|array|min:1',
            'items.*.part_number' => 'required|string|max:100',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.notes'       => 'nullable|string|max:255',
        ], [
            'items.required'               => 'Data items wajib diisi.',
            'items.min'                    => 'Minimal 1 item harus diisi.',
            'items.*.part_number.required' => 'Nomor part wajib diisi.',
            'items.*.quantity.required'    => 'Quantity wajib diisi.',
            'items.*.quantity.integer'     => 'Quantity harus berupa angka bulat.',
            'items.*.quantity.min'         => 'Quantity minimal 1.',
        ]);

        // Gabungkan duplikat part_number
        $merged = collect($request->items)
            ->groupBy('part_number')
            ->map(fn($group, $partNumber) => [
                'part_number' => $partNumber,
                'quantity'    => $group->sum('quantity'),
                'notes'       => $group->last()['notes'] ?? null,
            ])
            ->values();

        // Replace semua items lama
        $attendance->items()->delete();
        $attendance->items()->createMany($merged->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Items berhasil disimpan.',
            'data'    => [
                'attendance_id' => $attendance->id,
                'store_name'    => $attendance->store_name,
                'total_items'   => $merged->count(),
                'total_qty'     => $merged->sum('quantity'),
                'items'         => $merged->values(),
            ],
        ]);
    }

    // ── POST /api/attendances/{attendance}/items/import ───────────────────
    public function import(Request $request, Attendance $attendance)
    {
        if ($attendance->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes'    => 'File harus berformat xlsx atau xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $import = new AttendanceItemsImport();
        Excel::import($import, $request->file('file'));

        $validItems  = $import->getValidItems();
        $warnings    = $import->getWarnings();

        if (empty($validItems)) {
            return response()->json([
                'success'  => false,
                'message'  => 'Tidak ada data valid yang dapat diimport.',
                'warnings' => $warnings,
            ], 422);
        }

        // Replace semua items lama
        $attendance->items()->delete();
        $attendance->items()->createMany($validItems);

        return response()->json([
            'success'  => true,
            'message'  => 'Import berhasil.',
            'data'     => [
                'attendance_id' => $attendance->id,
                'store_name'    => $attendance->store_name,
                'total_items'   => count($validItems),
                'total_qty'     => array_sum(array_column($validItems, 'quantity')),
                'items'         => $validItems,
            ],
            'warnings' => $warnings,
        ]);
    }

    // ── GET /api/items/template ───────────────────────────────────────────
    public function downloadTemplate()
    {
        return Excel::download(
            new AttendanceItemTemplateExport(),
            'template-input-part.xlsx'
        );
    }
}