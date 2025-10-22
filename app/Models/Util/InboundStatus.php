<?php
namespace App\Models\Util;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InboundStatus extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inbound_status_master';

    protected $guarded = [];

    protected $casts = [
        'submit_without_fill' => 'boolean'
    ];
    public function sub()
    {
        return $this->hasMany(InboundStatus::class, 'parent_id', 'id')->orderBy('sorting', 'asc');
    }
}