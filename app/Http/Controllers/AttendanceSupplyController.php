<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSupply;
use App\Models\Part;
use Illuminate\Http\Request;

class AttendanceSupplyController extends Controller
{
    public function edit(Attendance $attendance)
    {
        $attendance->load(['user', 'items']);

        // Ambil existing supplies
        $existingSupplies = $attendance->supplies()
            ->get()
            ->keyBy('kode_part');

        // Build supply data dari items
        $kodeParts = $attendance->items->pluck('kode_part')->unique();
        $partsMap  = Part::withTrashed()
            ->whereIn('kode_part', $kodeParts)
            ->get()
            ->keyBy('kode_part');

        $supplies = $attendance->items->map(function ($item) use ($existingSupplies, $partsMap) {
            $existing = $existingSupplies->get($item->kode_part);
            return [
                'kode_part'          => $item->kode_part,
                'deskripsi_part'     => $partsMap[$item->kode_part]?->deskripsi_part ?? '—',
                'quantity_requested' => $item->quantity,
                'quantity_supplied'  => $existing?->quantity_supplied ?? $item->quantity,
                'notes'              => $existing?->notes ?? '',
            ];
        });

        return view('attendances.supply', compact('attendance', 'supplies'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'supplies'                        => 'required|array',
            'supplies.*.kode_part'            => 'required|string',
            'supplies.*.quantity_requested'   => 'required|integer|min:0',
            'supplies.*.quantity_supplied'    => 'required|integer|min:0',
            'supplies.*.notes'                => 'nullable|string|max:255',
        ]);

        // Delete existing dan replace
        $attendance->supplies()->delete();

        $toInsert = collect($request->supplies)->map(fn($s) => [
            'attendance_id'      => $attendance->id,
            'kode_part'          => $s['kode_part'],
            'quantity_requested' => $s['quantity_requested'],
            'quantity_supplied'  => $s['quantity_supplied'],
            'notes'              => $s['notes'] ?? null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ])->toArray();

        AttendanceSupply::insert($toInsert);

        return redirect()
            ->route('attendances.show', $attendance)
            ->with('success', 'Data supply berhasil disimpan.');
    }
}