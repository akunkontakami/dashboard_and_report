<?php

namespace App\Models\Ticket;

use App\Models\Account\User;
use App\Models\Ticket\TicketHistory;
use App\Models\Util\CompanyProductSubject;
use App\Models\Util\EscalationTeam;
use App\Models\Util\HelpdeskCategory;
use App\Models\Util\MarketingCampaign;
use App\Models\Util\Product;
use App\Traits\FilterAgent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory, HasUuids, SoftDeletes,FilterAgent;

    protected $table = 'tickets';

    protected $guarded = [];

    protected $casts = [
        'sla_resolution_time' => 'array',
        'sla_response_time' => 'array',
        'sla_division' => 'array',
        'is_read' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function subject()
    {
        return $this->belongsTo(CompanyProductSubject::class, 'subject_id');
    }

    public function escalationTeam()
    {
        return $this->belongsTo(EscalationTeam::class, 'escalation_team_id');
    }


    public function agent(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'current_agent_id')
            ->join('company_users', 'company_users.user_id', 'users.id');
    }

    public function spv(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'spv_id')
            ->join('company_users', 'company_users.user_id', 'users.id');
    }


    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'marketing_campaign_id');
    }


    public function helpdesk(): HasOne
    {
        return $this->hasOne(HelpdeskCategory::class, 'id', 'helpdesk_id');
    }

    public function lastHistory(): HasOne
    {
        return $this->hasOne(TicketHistory::class, 'ticket_id', 'id')->latest();
    }

    public function ticketStatus(): HasMany
    {
        return $this->hasMany(Ticket::class, 'current_agent_id', 'current_agent_id')
            ->select([
                'status',
                'escalation_status',
                'status_id',
                'current_agent_id',
                'type',
                'company_id',
                DB::raw("count(distinct id) total")
            ])
            ->groupBy([
                "status",
                "escalation_status",
                "status_id",
                "spv_id",
                "current_agent_id",
                "type",
                "company_id"
            ]);
    }

    public function scopeFilterByCompanyTypeDateRangeAndSource($query, $companyId, $type, $dates, $source = null)
    {
        $query->where('tickets.company_id', $companyId)
            ->where('tickets.type', $type)
            ->whereRaw('date(tickets.created_at) in (?,?)', $dates)
            ->when($source, fn($query) => $query->whereIn('tickets.source', $source));
    }

    public function scopeActive($query)
    {
        $query->where('tickets.is_bucket', 0)
            ->where('tickets.visible', 1);
    }


    public function scopeFilterTicketSla($query, $ticketSla)
    {
        if ($ticketSla && count($ticketSla) == 1) {
            // fulfilled or breached
            if (in_array('fulfilled', $ticketSla)) {
                // masih hijau fulfilled
                $query->whereRaw("(
                    CASE 
                    when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration')) is not null and JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration'))!=''
                        then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration'))
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_at')) != '' THEN
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.end_at')), 
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_at'))
                            ), '%H:%i')
                    ELSE
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.end_at')), 
                                    NOW()
                            ), '%H%i')
                    END not like '-%' or tickets.sla_resolution_time is null
                )");
            } else {
                // sudah merah breached
                $query->whereRaw("CASE 
                    when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration')) is not null  and JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration'))!=''
                        then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_duration'))
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_at')) != '' THEN
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.end_at')), 
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.solved_at'))
                            ), '%H:%i')
                    ELSE
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_resolution_time, '$.end_at')), 
                                    NOW()
                            ), '%H%i')
                    END like '-%'
                ");
            }
        }
    }

    public function scopeFilterDivisionSla($query, $ticketSla)
    {
        if ($ticketSla && count($ticketSla) == 1) {
            // fulfilled or breached
            if (in_array('fulfilled', $ticketSla)) {
                // masih hijau fulfilled
                $query->whereRaw("(
                    CASE 
                    when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration')) is not null  and  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration')) !=''  
                        then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration'))
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_at')) != '' THEN
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.end_at')), 
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_at'))
                            ), '%H:%i')
                    ELSE
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.end_at')), 
                                    NOW()
                            ), '%H%i')
                    END not like '-%' or tickets.sla_division is null
                ) ");
            } else {
                // sudah merah breached
                $query->whereRaw("CASE 
                    when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration')) is not null and  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration')) !=''  
                        then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_duration'))
                    WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_at')) != '' THEN
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.end_at')), 
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.solved_at'))
                            ), '%H:%i')
                    ELSE
                            TIME_FORMAT(TIMEDIFF(
                                    JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_division, '$.end_at')), 
                                    NOW()
                            ), '%H%i')
                    END like '-%'
                ");
            }
        }
    }

    public function scopeFilterResponseTimeSla($query, $responseSla)
    {
        if (count($responseSla ?: [])) {
            $fullfiledTicket = in_array('fulfilled', $responseSla);
            $breachedTicket = in_array('breached', $responseSla);
            // fulfilled or breached
            if (!$fullfiledTicket || !$breachedTicket) {
                if (in_array('fulfilled', $responseSla)) {

                    // masih hijau fulfilled
                    $query->whereRaw("(
                        CASE 
                        when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration')) is not null and JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration')) !=''
                            then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration'))
                        WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_at')) != '' THEN
                                TIME_FORMAT(TIMEDIFF(
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.end_at')), 
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_at'))
                                ), '%H:%i')
                        ELSE
                                TIME_FORMAT(TIMEDIFF(
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.end_at')), 
                                        NOW()
                                ), '%H%i')
                        END not like '-%' or tickets.sla_response_time is null
                        )
                    ");
                } else {
                    // sudah merah breached
                    $query->whereRaw("CASE 
                        when  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration')) is not null and JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration')) !=''
                            then  JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_duration'))
                        WHEN JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_at')) != '' THEN
                                TIME_FORMAT(TIMEDIFF(
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.end_at')), 
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.solved_at'))
                                ), '%H:%i')
                        ELSE
                                TIME_FORMAT(TIMEDIFF(
                                        JSON_UNQUOTE(JSON_EXTRACT(tickets.sla_response_time, '$.end_at')), 
                                        NOW()
                                ), '%H%i')
                        END like '-%' 
                    ");
                }
            }

        }
    }

    public function scopeFilterBroadcasted($query, $broadcast_response)
    {
        $query->when($broadcast_response == 'yes', fn($query) => $query->where('tickets.is_broadcasted', true))
            ->when($broadcast_response == 'no', fn($query) => $query->where(function ($query) {
                $query->where('tickets.is_broadcasted', false);
                $query->orWhereNull('tickets.is_broadcasted');
            }));
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('tickets.ticket_number', 'like', "%{$search}%");
                $query->orWhere('tickets.source', 'like', "%{$search}%");
                $query->orWhere('tickets.customer_name', 'like', "%{$search}%");
                $query->orWhere('tickets.status', 'like', "%{$search}%");
                $query->orWhere('tickets.product_category', 'like', "%{$search}%");
                $query->orWhereRelation('product', 'name', 'like', "%{$search}%");
            });
        }
    }
}
