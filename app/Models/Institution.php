<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'agency',
        'system_name',
        'system_subtitle',
        'address',
        'phone',
        'email',
        'logo',
        'active_fiscal_year',
    ];
}
