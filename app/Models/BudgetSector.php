<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetSector extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'color',
        'icon',
    ];

    public function apbdesItems(): HasMany
    {
        return $this->hasMany(ApbdesItem::class);
    }

    public function expenditures(): HasMany
    {
        return $this->hasMany(Expenditure::class);
    }
}
