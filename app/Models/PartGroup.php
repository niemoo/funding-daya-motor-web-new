<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}
