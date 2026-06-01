<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceItem extends Model
{
    protected $fillable = [
        'attendance_id',
        'kode_part',
        'quantity',
        'notes',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'kode_part', 'kode_part')->withTrashed();
    }
}
