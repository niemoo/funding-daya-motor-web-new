<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false; // hanya pakai created_at

    protected $fillable = [
        'user_id',
        'model_type',
        'model_id',
        'field_name',
        'old_value',
        'new_value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper: apakah field ini adalah items (value-nya JSON)
    public function isItemsLog(): bool
    {
        return $this->field_name === 'items';
    }

    // Helper: decode old/new value kalau JSON
    public function oldValueDecoded(): mixed
    {
        if ($this->isItemsLog()) {
            return json_decode($this->old_value, true);
        }
        return $this->old_value;
    }

    public function newValueDecoded(): mixed
    {
        if ($this->isItemsLog()) {
            return json_decode($this->new_value, true);
        }
        return $this->new_value;
    }
}