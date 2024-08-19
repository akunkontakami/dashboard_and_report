<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketingCampaignUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_marketing_campaigns';

    protected $guarded = [];

}
