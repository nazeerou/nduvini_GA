<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'branch_id', 'module', 'reference_table', 'reference_id', 'reference_no',
        'subject', 'details', 'amount', 'status',
        'requested_by', 'requested_at',
        'reviewed_by', 'reviewed_at', 'review_comment', 'rejection_reason',
        'paid_by', 'paid_at', 'payment_ref',
    ];

    protected $casts = [
        'details'       => 'array',
        'requested_at'  => 'datetime',
        'reviewed_at'   => 'datetime',
        'paid_at'       => 'datetime',
        'amount'        => 'decimal:2',
    ];

    /* ---- Relationships ---- */
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer()  { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function payer()     { return $this->belongsTo(User::class, 'paid_by'); }
    public function timeline()  { return $this->hasMany(ApprovalTimeline::class); }

    /* ---- Scopes ---- */
    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopeForBranch($q, $branchId) { return $q->where('branch_id', $branchId); }

    /* ---- Display helpers ---- */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'  => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid'     => 'Paid',
            default    => ucfirst($this->status),
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'  => 'status-pending',
            'approved' => 'status-approved',
            'rejected' => 'status-rejected',
            'paid'     => 'status-paid',
            default    => 'status-pending',
        };
    }

    public function statusIcon(): string
    {
        return match ($this->status) {
            'pending'  => 'fa-clock-o',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'paid'     => 'fa-money',
            default    => 'fa-clock-o',
        };
    }
}