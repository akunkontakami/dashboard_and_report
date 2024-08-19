<?php

namespace App\Http\Resources\Report;

use App\Helpers\Yellow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketListReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $slaDivision = Yellow::getDurationSlaTimer($this->sla_division, now());
        $slaResponseTime = Yellow::getDurationSlaTimer($this->sla_response_time, now());
        $slaResolutionTime = Yellow::getDurationSlaTimer($this->sla_resolution_time, now());

        $status = $this->escalation_status ?: $this->status;
        $statusColor = in_array($status,['New','From Bot','From Whatsapp Bot']) ? 'bg-offline' : match ($this->status_category) {
            'Closed' => 'bg-online',
            'Solved' => 'bg-blue',
            'Open' => 'bg-yellow',
            'New' => 'bg-offline',
            default => 'bg-online',
        };

        return [
            ...parent::toArray($request),
            'date' => date('d M Y', strtotime($this->created_at)),
            'updated_at' => date('d M Y', strtotime($this->updated_at)),
            'helpdesk_name' => $this->product_category == 'Other' ? 'Other' : $this->helpdesk_name,
            'priority_color' => match ($this->priority) {
                'Low' => 'bg-online',
                'Medium' => 'bg-yellow',
                'High' => 'bg-offline',
                'Critical' => 'bg-offline',
                'Urgent' => 'bg-offline',
                default => 'bg-online',
            },
            'status_color' => str_contains(strtolower($status), 'closed') ? 'bg-online' : $statusColor,
            'sla_response_time' => $slaResponseTime,
            'sla_resolution_time' => $slaResolutionTime,
            'sla_division' => $slaDivision,
            'sla_response_time_color' => str_contains($slaResponseTime, '-') ? '#FE4C4C' : '#38A363',
            'sla_resolution_time_color' => str_contains($slaResolutionTime, '-') ? '#FE4C4C' : '#38A363',
            'sla_division_color' => str_contains($slaDivision, '-') ? '#FE4C4C' : '#38A363',
            'sla_response_time_time' => $this->sla_response_time,
            'sla_resolution_time_time' => $this->sla_resolution_time,
            'sla_division_time_time' => $this->sla_division,
            'is_broadcasted' => $this->is_broadcasted ? 'Yes' : 'No',
        ];
    }
}
