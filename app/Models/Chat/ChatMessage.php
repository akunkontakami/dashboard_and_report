<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'chat_messages';

    protected $guarded = [];

    protected function message(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                switch ($this->message_type) {
                    case 'message':
                        return $value;
                    case 'information':
                        return $value;
                    default:
                        return $value ? json_decode($value) : $value;
                }
            },
        );
    }
}
