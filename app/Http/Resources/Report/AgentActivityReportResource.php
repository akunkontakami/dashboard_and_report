<?php

namespace App\Http\Resources\Report;

use App\Helpers\Yellow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentActivityReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'date' => date('d M Y', strtotime($this->date)),
            'name' => "{$this->code} - {$this->name}",
            'role' => $this->role == 'spv' ? 'SPV' : ucwords($this->role),
            'login_time' => Yellow::secondsToHoursMinutes($this->login_time),
            'offline' => Yellow::secondsToHoursMinutes($this->offline),
            'talktime' => Yellow::secondsToHoursMinutes($this->type_category === 'inbound' ? $this->talktime_inbound : $this->talktime_outbound),
            'available_time' => Yellow::secondsToHoursMinutes($this->available_time),
        ];
    }
}
