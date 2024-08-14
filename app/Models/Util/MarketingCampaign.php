<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MarketingCampaign extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'marketing_campaigns';

    protected $guarded = [];
}
