<?php
namespace App\Models\Util;


use App\Models\Account\User;
use App\Models\Account\CompanyUser;
use App\Models\Company\Company;
use App\Models\Data\HelpdeskCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserHelpdesk extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'user_helpdesk';

    protected $guarded = [];
}