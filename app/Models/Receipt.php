<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'transaction_number',
        'date',
        'funding_source',
        'description',
        'amount',
        'payer',
        'destination_account',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'diverifikasi' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'tercatat' => 'bg-blue-100 text-blue-800 border-blue-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
