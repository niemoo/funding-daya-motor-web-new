<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Part extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'part_group_id',
        'kode_part',
        'deskripsi_part',
    ];

    public function group()
    {
        return $this->belongsTo(PartGroup::class, 'part_group_id');
    }
}
