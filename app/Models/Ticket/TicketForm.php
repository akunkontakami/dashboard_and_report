<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketForm extends Model
{
    use HasFactory,HasUuids;
    protected $table = 'ticket_forms';

    protected $guarded = [];
}
