<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\ApprovalTimeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public static function submit(string $module, string $table, int $id, string $referenceNo, string $subject, float $amount, array $details): ApprovalRequest
    {
        return DB::transaction(function () use ($module, $table, $id, $referenceNo, $subject, $amount, $details) {
            // If a request was previously rejected, mark it revised
            ApprovalRequest::where('module', $module)
                ->where('reference_id', $id)
                ->where('status', 'rejected')
                ->update(['status' => 'revised']);

            $req = ApprovalRequest::create([
                'branch_id'       => Auth::user()->branch_id,
                'module'          => $module,
                'reference_table' => $table,
                'reference_id'    => $id,
                'reference_no'    => $referenceNo,
                'subject'         => $subject,
                'details'         => $details,
                'amount'          => $amount,
                'status'          => 'pending',
                'requested_by'    => Auth::id(),
                'requested_at'    => now(),
            ]);

            self::log($req->id, 'submitted', 'Submitted for HR approval');
            return $req;
        });
    }

    public static function approve(ApprovalRequest $req, ?string $comment = null): void
    {
        DB::transaction(function () use ($req, $comment) {
            $req->update([
                'status'         => 'approved',
                'reviewed_by'    => Auth::id(),
                'reviewed_at'    => now(),
                'review_comment' => $comment,
            ]);
            self::log($req->id, 'approved', $comment ?: 'Approved by HR');
        });
    }

    public static function reject(ApprovalRequest $req, string $reason): void
    {
        DB::transaction(function () use ($req, $reason) {
            $req->update([
                'status'           => 'rejected',
                'reviewed_by'      => Auth::id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $reason,
            ]);
            self::log($req->id, 'rejected', $reason);
        });
    }

    public static function markPaid(ApprovalRequest $req, ?string $paymentRef = null): void
    {
        DB::transaction(function () use ($req, $paymentRef) {
            $req->update([
                'status'      => 'paid',
                'paid_by'     => Auth::id(),
                'paid_at'     => now(),
                'payment_ref' => $paymentRef,
            ]);
            self::log($req->id, 'paid', 'Payment completed' . ($paymentRef ? " (Ref: $paymentRef)" : ''));
        });
    }

    protected static function log(int $reqId, string $action, ?string $note = null): void
    {
        ApprovalTimeline::create([
            'approval_request_id' => $reqId,
            'action'              => $action,
            'actor_id'            => Auth::id(),
            'note'                => $note,
            'created_at'          => now(),
        ]);
    }
}