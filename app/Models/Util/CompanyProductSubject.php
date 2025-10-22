<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProductSubject extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'company_product_subjects';

    protected $guarded = [];

}
