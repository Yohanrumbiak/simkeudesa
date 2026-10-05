<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApbdesItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'apbdes_id',
        'type',
        'budget_sector_id',
        'account_code',
        'activity_name',
        'original_amount',
        'revised_amount',
    ];

    protected function casts(): array
    {
        return [
            'original_amount' => 'decimal:2',
            'revised_amount' => 'decimal:2',
        ];
    }

    public function apbdes(): BelongsTo
    {
        return $this->belongsTo(Apbdes::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(BudgetSector::class, 'budget_sector_id');
    }

    public function expenditures(): HasMany
    {
        return $this->hasMany(Expenditure::class);
    }

    public function getEffectiveAmountAttribute(): float
    {
        return (float) ($this->revised_amount > 0 ? $this->revised_amount : $this->original_amount);
    }

    public function getRealizedAmountAttribute(): float
    {
        return (float) $this->expenditures()
            ->whereIn('status', ['diverifikasi', 'dibayar'])
            ->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0.0, $this->effective_amount - $this->realized_amount);
    }

    public function getAbsorptionRateAttribute(): float
    {
        if ($this->effective_amount <= 0) {
            return 0.0;
        }
        return round(($this->realized_amount / $this->effective_amount) * 100, 1);
    }
}
