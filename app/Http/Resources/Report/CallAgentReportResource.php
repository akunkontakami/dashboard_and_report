<?php

namespace App\Http\Resources\Report;

use App\Helpers\Yellow;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallAgentReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => date('d M Y',strtotime($this->date)),
            'agent_id' => $this->agent_id,
            'agent_name' => $this->agent_name,
            'agent_code' => $this->agent_code,
            'spv_name' => $this->spv_code .' - '. $this->spv_name,
            'total_call' => $this->total_call,
            'incoming' => $this->incoming,
            'outgoing' => $this->outgoing,
            'missed' => $this->missed,
            'callback' => $this->callback,
            'talktime' => Yellow::secondsToHoursMinutes($this->talktime),
            'avg_talktime' => ($this->incoming && $this->talktime) ? Yellow::secondsToHoursMinutes($this->talktime/$this->incoming) : '00:00:00',
        ];
    }
}
