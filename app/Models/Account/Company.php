<?php
namespace App\Models\Account;

use App\Models\Data\Category;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'companies';
    protected $guarded = [];

}
