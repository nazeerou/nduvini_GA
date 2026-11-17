<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'period_month' => 'integer',
        'period_year'  => 'integer',
        'amount'       => 'decimal:2',
        'employee_count' => 'integer',
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'payload'      => 'array',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // User who submitted the request
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // User assigned to receive and approve the request
    public function assignedApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // User who actually approved the request
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // User who rejected the request
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(
            PayrollRequestEvent::class,
            'payroll_request_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // Pending requests assigned to the logged-in approver
    public function scopeApproverInbox($query, $userId)
    {
        return $query->where('status', 'pending')
                     ->where('approver_id', $userId);
    }

    // Requests submitted by a particular user
    public function scopeRequestedBy($query, $userId)
    {
        return $query->where('requested_by', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {

            'pending' => '<span class="label label-warning">Pending</span>',

            'approved' => '<span class="label label-success">Approved</span>',

            'rejected' => '<span class="label label-danger">Rejected</span>',

            'paid' => '<span class="label label-primary">Paid</span>',

            'cancelled' => '<span class="label label-default">Cancelled</span>',

            default => '<span class="label label-default">Draft</span>',
        };
    }
}
