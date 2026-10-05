<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'subdistrict',
        'head_name',
        'phone',
        'address',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function apbdes(): HasMany
    {
        return $this->hasMany(Apbdes::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function expenditures(): HasMany
    {
        return $this->hasMany(Expenditure::class);
    }

    public function disbursementApplications(): HasMany
    {
        return $this->hasMany(DisbursementApplication::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(VillageAsset::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get APBDes for a specific fiscal year
     */
    public function getApbdesForYear(int $year): ?Apbdes
    {
        return $this->apbdes()->where('fiscal_year', $year)->latest()->first();
    }

    /**
     * Get Total Budget (Belanja) for fiscal year
     */
    public function getTotalBudgetForYear(int $year): float
    {
        $apbdes = $this->getApbdesForYear($year);
        return $apbdes ? (float) $apbdes->total_expenditure_budget : 0.0;
    }

    /**
     * Get Total Receipts for fiscal year
     */
    public function getTotalReceiptsForYear(int $year): float
    {
        return (float) $this->receipts()
            ->where('fiscal_year', $year)
            ->where('status', 'diverifikasi')
            ->sum('amount');
    }

    /**
     * Get Total Expenditures (Realization) for fiscal year
     */
    public function getTotalExpendituresForYear(int $year): float
    {
        return (float) $this->expenditures()
            ->where('fiscal_year', $year)
            ->whereIn('status', ['diverifikasi', 'dibayar'])
            ->sum('amount');
    }

    /**
     * Get Budget Absorption Rate in percent
     */
    public function getAbsorptionRateForYear(int $year): float
    {
        $budget = $this->getTotalBudgetForYear($year);
        if ($budget <= 0) {
            return 0.0;
        }
        $realization = $this->getTotalExpendituresForYear($year);
        return round(($realization / $budget) * 100, 1);
    }

    /**
     * Get Remaining Budget
     */
    public function getRemainingBudgetForYear(int $year): float
    {
        $budget = $this->getTotalBudgetForYear($year);
        $realization = $this->getTotalExpendituresForYear($year);
        return max(0.0, $budget - $realization);
    }
}
