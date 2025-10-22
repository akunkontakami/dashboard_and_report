<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueActionLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'queue_action_logs';
    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];
}
