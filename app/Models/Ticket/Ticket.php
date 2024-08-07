<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'tickets';

    protected $guarded = [];

    protected $casts = [
        'sla_resolution_time' => 'array',
        'sla_response_time' => 'array',
        'sla_division' => 'array',
        'is_read' => 'boolean'
    ];

    public function scopeFilterByCompanyTypeDateRangeAndSource($query, $companyId, $type, $dates, $source = null)
    {
        $query->where('tickets.company_id', $companyId)
            ->where('tickets.type', $type)
            ->whereRaw('date(tickets.created_at) in (?,?)', $dates)
            ->when($source, fn($query) => $query->whereIn('tickets.source', $source));
    }

}
