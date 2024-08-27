<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'inbound_whatsapp_chat';

    protected $guarded = [];

    protected function message(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                switch ($this->message_type) {
                    case 'text':
                        return $value;
                    case 'broadcast':
                        return "Broadcast Message";
                    default:
                        return $value ? json_decode($value) : $value;
                }
            },
        );
    }
}
