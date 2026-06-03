<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GeneralStore;
use Illuminate\Http\Request;

class GeneralStoreApiController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:2|max:100',
        ], [
            'search.required' => 'Keyword pencarian wajib diisi.',
            'search.min'      => 'Keyword minimal 2 karakter.',
        ]);

        $stores = GeneralStore::query()
        ->where('name', 'like', '%' . $request->search . '%')
        ->orderBy('name')
        ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'message' => 'Pencarian general store berhasil.',
            'total_data'   => $stores->count(),
            'data'    => $stores,
        ]);
    }
}