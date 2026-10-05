<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expenditure extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'transaction_number',
        'date',
        'budget_sector_id',
        'apbdes_item_id',
        'activity_name',
        'description',
        'amount',
        'payee',
        'payment_method',
        'supporting_document',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(BudgetSector::class, 'budget_sector_id');
    }

    public function apbdesItem(): BelongsTo
    {
        return $this->belongsTo(ApbdesItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'dibayar' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'diverifikasi' => 'bg-blue-100 text-blue-800 border-blue-300',
            'diajukan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'ditolak' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
