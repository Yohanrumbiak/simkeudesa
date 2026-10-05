<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'deadline_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deadline_date' => 'date',
        ];
    }

    public function getDaysRemainingAttribute(): int
    {
        return (int) now()->diffInDays($this->deadline_date, false);
    }
}
