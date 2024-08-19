<?php
namespace App\Models\Util;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Billing extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'company_billings';

    protected $guarded = [];

    protected $casts = [
        'summary' => 'array',
        'additional' => 'array'
    ];

    public function scopeAvailable($query, $date)
    {
        $query->where('available_from', '<=', $date)
            ->where('available_until', '>=', $date);
    }
}