<?php

namespace App\Models\Ticket;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketBeneficiary extends Model
{
    use HasFactory,HasUuids;
    
    protected $table = 'ticket_insured_beneficiaries';

    protected $guarded = [];
}
