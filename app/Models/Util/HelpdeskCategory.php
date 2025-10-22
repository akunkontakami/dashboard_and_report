<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpdeskCategory extends Model
{
    use HasFactory, HasUuids,SoftDeletes;

    protected $table = 'company_helpdesk_categories';

    protected $guarded = [];

    protected $casts = [
        'is_hidden' => 'boolean'
    ];
    
    public function sub()
    {
        return $this->hasMany(HelpdeskCategory::class, 'parent_id', 'id')->orderBy('sorting', 'asc');
    }
}
