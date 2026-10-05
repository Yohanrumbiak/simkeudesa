<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisbursementApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'village_id',
        'fiscal_year',
        'application_number',
        'title',
        'phase',
        'requested_amount',
        'submission_date',
        'status',
        'review_notes',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'verified_at' => 'datetime',
            'requested_amount' => 'decimal:2',
        ];
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DisbursementDocument::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DisbursementReview::class)->latest();
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'lengkap' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'disalurkan' => 'bg-green-100 text-green-800 border-green-300',
            'menunggu_verifikasi' => 'bg-amber-100 text-amber-800 border-amber-300',
            'perlu_revisi' => 'bg-orange-100 text-orange-800 border-orange-300',
            'ditolak' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'lengkap' => 'Dokumen Lengkap / Disetujui',
            'disalurkan' => 'Dana Telah Disalurkan',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'perlu_revisi' => 'Perlu Perbaikan / Revisi',
            'ditolak' => 'Pengajuan Ditolak',
            default => 'Status Tidak Diketahui',
        };
    }
}
