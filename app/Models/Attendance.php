<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'attendance_date',
        'checkin_time',
        'checkin_latitude',
        'checkin_longitude',
        'checkin_photo',
        'store_name',
        'person_in_charge_name',
        'person_in_charge_phone',
        'checkout_time',
        'checkout_latitude',
        'checkout_longitude',
        'checkout_photo',
        'work_duration_minutes',
        'is_auto_checkout',
    ];

    protected $casts = [
        'attendance_date'  => 'date',
        'checkin_time'     => 'datetime',
        'checkout_time'    => 'datetime',
        'is_auto_checkout' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
