<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceItem extends Model
{
    protected $fillable = [
        'attendance_id',
        'part_number',
        'quantity',
        'notes',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
