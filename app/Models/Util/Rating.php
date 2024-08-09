<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory,HasUuids;

    protected $table = 'ratings';

    protected $guarded = [];

    protected $casts = [
        'csat_rating' => 'array'
    ];
}
