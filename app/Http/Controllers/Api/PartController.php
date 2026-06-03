<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ], [
            'search.required' => 'Keyword pencarian wajib diisi.',
            'search.min'      => 'Keyword minimal 2 karakter.',
        ]);

        $search = $request->search;

        $parts = Part::where('kode_part', 'like', '%' . $search . '%')
            ->orWhere('deskripsi_part', 'like', '%' . $search . '%')
            ->orderByRaw("
                CASE
                    WHEN kode_part LIKE ? THEN 1
                    WHEN deskripsi_part LIKE ? THEN 2
                    ELSE 3
                END
            ", [$search . '%', $search . '%'])
            ->limit(10)
            ->get(['kode_part', 'deskripsi_part']);

        return response()->json([
            'success' => true,
            'message' => 'Pencarian part berhasil.',
            'total_data'   => $parts->count(),
            'data'    => $parts,
        ]);
    }
}