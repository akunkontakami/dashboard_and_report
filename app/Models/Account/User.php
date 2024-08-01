<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'users';

    protected $guarded = [];

    protected $hidden = ['password'];

    protected $fileField = ['profile'];
}
