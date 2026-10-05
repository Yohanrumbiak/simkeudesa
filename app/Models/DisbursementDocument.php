<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisbursementDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_application_id',
        'document_name',
        'file_path',
        'file_size',
        'file_type',
        'status',
        'notes',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(DisbursementApplication::class, 'disbursement_application_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'lengkap' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'perlu_revisi' => 'bg-amber-100 text-amber-800 border-amber-300',
            'ditolak' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-blue-100 text-blue-800 border-blue-300',
        };
    }
}
