<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apbdes extends Model
{
    use HasFactory;

    protected $table = 'apbdes';

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'title',
        'document_number',
        'type',
        'total_revenue_budget',
        'total_expenditure_budget',
        'total_financing_budget',
        'status',
        'approval_date',
        'approved_by',
        'notes',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'approval_date' => 'date',
            'total_revenue_budget' => 'decimal:2',
            'total_expenditure_budget' => 'decimal:2',
            'total_financing_budget' => 'decimal:2',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ApbdesItem::class);
    }

    public function revenueItems(): HasMany
    {
        return $this->hasMany(ApbdesItem::class)->where('type', 'pendapatan');
    }

    public function expenditureItems(): HasMany
    {
        return $this->hasMany(ApbdesItem::class)->where('type', 'belanja');
    }

    public function financingItems(): HasMany
    {
        return $this->hasMany(ApbdesItem::class)->where('type', 'pembiayaan');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'diverifikasi' => 'bg-blue-100 text-blue-800 border-blue-300',
            'diajukan' => 'bg-amber-100 text-amber-800 border-amber-300',
            'ditolak' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
