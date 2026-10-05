<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VillageAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'asset_code',
        'name',
        'category',
        'acquisition_year',
        'acquisition_value',
        'quantity',
        'unit',
        'location',
        'condition',
        'maintenance_notes',
        'photo_path',
    ];

    protected function casts(): array
    {
        return [
            'acquisition_year' => 'integer',
            'acquisition_value' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            'baik' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'rusak_ringan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'rusak_berat' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Tidak Diketahui',
        };
    }
}
