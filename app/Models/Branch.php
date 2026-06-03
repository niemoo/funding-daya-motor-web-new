<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = ['kode_cabang', 'nama_cabang'];

    public function stockLocators()
    {
        return $this->hasMany(StockLocator::class);
    }
}