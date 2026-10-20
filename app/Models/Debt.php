<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['user_id', 'branch_id', 'category_id', 'particulars', 'amount', 'month', 'year', 'created_by'];

    protected $casts = [
        'amount' => 'decimal:2',
        'month'  => 'integer',
        'year'   => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function category()
    {
        return $this->belongsTo(DebtCategory::class, 'category_id');
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * User who recorded the debt (uses the user_id column).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * User who originally created the record (uses created_by).
     * Kept separate from user() in case they diverge historically.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ---------------- Computed attributes ---------------- */

    // Uses withSum('payments','amount') when available (avoids N+1)
    public function getPaidAmountAttribute(): float
    {
        if (array_key_exists('payments_sum_amount', $this->attributes)) {
            return (float) ($this->attributes['payments_sum_amount'] ?? 0);
        }
        return (float) $this->payments()->sum('amount');
    }

    public function getUnpaidAmountAttribute(): float
    {
        return max(0, (float) $this->amount - $this->paid_amount);
    }

    public function getStatusAttribute(): string
    {
        if ($this->unpaid_amount <= 0) {
            return 'Paid';
        }
        return $this->paid_amount > 0 ? 'Partial' : 'Unpaid';
    }

    public function getPeriodAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->format('F Y');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeFilter($query, array $filters)
    {
        $paidSub = '(SELECT COALESCE(SUM(dp.amount),0) FROM debt_payments dp WHERE dp.debt_id = debts.id)';

        $query->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
              ->when($filters['month'] ?? null,       fn ($q, $v) => $q->where('month', $v))
              ->when($filters['year'] ?? null,        fn ($q, $v) => $q->where('year', $v))
              ->when($filters['search'] ?? null,      fn ($q, $v) => $q->where('particulars', 'like', "%{$v}%"));

        switch ($filters['status'] ?? null) {
            case 'paid':
                $query->whereRaw("{$paidSub} >= debts.amount");
                break;
            case 'partial':
                $query->whereRaw("{$paidSub} > 0 AND {$paidSub} < debts.amount");
                break;
            case 'unpaid':
                $query->whereRaw("{$paidSub} = 0");
                break;
            case 'outstanding':
                $query->whereRaw("{$paidSub} < debts.amount");
                break;
        }

        return $query;
    }
}