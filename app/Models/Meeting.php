<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'tracking_id',
        'full_name',
        'email',
        'phone_number',
        'company',
        'meeting_type',
        'duration',
        'scheduled_at',
        'timezone',
        'message',
        'status',
        'internal_notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration' => 'integer',
    ];
}
