<?php

namespace App\Models\Ticket;

use App\Models\Chat\ChatMessage;
use App\Models\Chat\WhatsappMessage;
use App\Models\Ticket\TicketBeneficiary;
use App\Models\Ticket\TicketComment;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'ticket_histories';

    protected $guarded = [];

    public function forms()
    {
        return $this->hasMany(TicketForm::class, 'ticket_history_id', 'id')
            ->select(['input_type', 'label','sorting', 'content', 'group_name', 'group_sorting', 'ticket_history_id','form_category','id'])
            ->orderBy('group_sorting', 'asc');
    }

    public function beneficiary()
    {
        return $this->hasMany(TicketBeneficiary::class, 'ticket_history_id', 'id')
            ->orderBy('sorting', 'asc');
    }

    public function insured()
    {
        return $this->hasOne(TicketInsured::class, 'ticket_history_id', 'id')
            ->orderBy('created_at', 'desc');
    }
}
