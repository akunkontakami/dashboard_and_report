<?php

namespace App\Models\Ticket;

use App\Models\Chat\ChatMessage;
use App\Models\Chat\WhatsappMessage;
use App\Models\Ticket\TicketBeneficiary;
use App\Models\Ticket\TicketComment;
use App\Traits\FilterAgent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    use HasFactory, HasUuids,FilterAgent;
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

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'chat_id')
            ->leftJoin('company_users', function ($join) {
                $join->on('chat_messages.user_id', 'company_users.user_id');
                $join->on('chat_messages.company_id', 'company_users.company_id');
            })
            ->select([
                'chat_messages.message',
                'chat_messages.message_type',
                'chat_messages.user_id',
                'chat_messages.chat_id',
                'chat_messages.created_at',
                'company_users.name'
            ])
            ->orderBy('chat_messages.created_at', 'asc');
    }

    public function wa()
    {
        return $this->hasMany(WhatsappMessage::class, 'inbound_whatsapp_id', 'inbound_whatsapp_id')
            ->leftJoin('company_users', function ($join) {
                $join->on('inbound_whatsapp_chat.sender_id', 'company_users.user_id');
                $join->on('inbound_whatsapp_chat.company_id', 'company_users.company_id');
                $join->where('inbound_whatsapp_chat.sender_role', 'agent');
            })
            ->select([
                'inbound_whatsapp_chat.content as message',
                'inbound_whatsapp_chat.type as message_type',
                'inbound_whatsapp_chat.sender_id as user_id',
                'inbound_whatsapp_chat.inbound_whatsapp_id',
                'inbound_whatsapp_chat.created_at',
                'company_users.name'
            ])
            ->orderBy('inbound_whatsapp_chat.created_at', 'asc');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'ticket_id')
            ->leftJoin('company_users', function ($join) {
                $join->on('ticket_comments.agent_id', 'company_users.user_id');
                $join->on('ticket_comments.company_id', 'company_users.company_id');
            })
            ->select([
                'ticket_comments.type',
                'ticket_comments.content',
                'ticket_comments.created_at',
                'ticket_comments.ticket_id',
                'company_users.name'
            ])
            ->orderBy('ticket_comments.created_at', 'asc');
    }

}
