<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisbursementReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'disbursement_application_id',
        'reviewer_id',
        'status_before',
        'status_after',
        'notes',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(DisbursementApplication::class, 'disbursement_application_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
