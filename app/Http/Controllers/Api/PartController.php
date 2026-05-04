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
            'keyword' => 'required|string|min:2|max:100',
        ], [
            'keyword.required' => 'Keyword pencarian wajib diisi.',
            'keyword.min'      => 'Keyword minimal 2 karakter.',
        ]);

        $keyword = $request->keyword;

        $parts = Part::where('kode_part', 'like', '%' . $keyword . '%')
            ->orWhere('deskripsi_part', 'like', '%' . $keyword . '%')
            ->orderByRaw("
                CASE
                    WHEN kode_part LIKE ? THEN 1
                    WHEN deskripsi_part LIKE ? THEN 2
                    ELSE 3
                END
            ", [$keyword . '%', $keyword . '%'])
            ->limit(10)
            ->get(['kode_part', 'deskripsi_part']);

        return response()->json([
            'success' => true,
            'message' => 'Pencarian part berhasil.',
            'data'    => $parts,
        ]);
    }
}