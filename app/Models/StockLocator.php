<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLocator extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'kode_part',
        'part_group_id',
        'lokasi_stock',
        'jumlah',
        'nilai_stock',
    ];

    protected $casts = [
        'jumlah'      => 'decimal:2',
        'nilai_stock' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id')->withTrashed();
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'kode_part', 'kode_part')->withTrashed();
    }

    public function group()
    {
        return $this->belongsTo(PartGroup::class, 'part_group_id')->withTrashed();
    }

    // Computed total
    public function getTotalNilaiAttribute(): float
    {
        return (float) $this->jumlah * (float) $this->nilai_stock;
    }
}