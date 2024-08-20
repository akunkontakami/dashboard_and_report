<?php

namespace App\Http\Resources\Report;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class CallTrackingReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ticketStatus = $this->ticketStatus->each(function ($row) {
            $escalationStatus = $row->escalation_status;
            $row->status = $row->status_id . "-" . Str::slug($row->status, '_');
            if ($escalationStatus) {
                $row->escalation_status = $escalationStatus === 'Closed' ? 'escalation-done' : 'escalation-in-progress';
            }
        });
        return [
            'agent_name' => $this->agent_name,
            'agent_code' => $this->agent_code,
            'spv_name' => $this->spv_name,
            'total_customer' => $this->total_customer,
            'total_ticket' => $this->total_ticket,
            'current_agent_id' => $this->current_agent_id,
            'spv_id' => $this->spv_id,
            'ticket_status' => [
                ...$ticketStatus->groupBy('status')->map(fn($row) => $row->sum(fn($key) => $key->total)),
                ...$ticketStatus->whereNotNull('escalation_status')->groupBy('escalation_status')->map(fn($row) => $row->sum(fn($key) => $key->total)),
            ],
        ];
    }
}
