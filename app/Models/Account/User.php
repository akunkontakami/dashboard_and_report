<?php

namespace App\Models\Account;

use App\Models\Account\CompanyUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'users';

    protected $guarded = [];

    protected $hidden = ['password'];

    public $casts = [
        'role' => Role::class
    ];


    public function companyUser(): BelongsTo
    {
        return $this->belongsTo(CompanyUser::class, 'id', 'user_id');
    }
}
