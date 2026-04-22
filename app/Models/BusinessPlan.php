<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_size',
        'category',
        'version',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'version' => 'integer',
    ];
}
