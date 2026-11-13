<?php

namespace App\Http\Controllers;

use App\Models\ApprovalRequest;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    protected function branchId() { return Auth::user()->branch_id; }

    /** HR: list pending approvals */
    public function inbox()
    {
        $pending  = ApprovalRequest::forBranch($this->branchId())
                        ->pending()->with('requester')->latest()->get();
        $approved = ApprovalRequest::forBranch($this->branchId())
                        ->approved()->with('reviewer')->latest()->limit(20)->get();
        $rejected = ApprovalRequest::forBranch($this->branchId())
                        ->where('status', 'rejected')->with('reviewer')->latest()->limit(20)->get();

        return view('approvals.inbox', compact('pending', 'approved', 'rejected'));
    }

    /** Show a single request with details + timeline */
    public function show($id)
    {
        $req = ApprovalRequest::forBranch($this->branchId())->with(['requester','reviewer','payer','timeline.actor'])->findOrFail($id);
        return response()->json([
            'html' => view('approvals._details', compact('req'))->render(),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $request->validate(['comment' => 'nullable|string|max:2000']);
        $req = ApprovalRequest::forBranch($this->branchId())->pending()->findOrFail($id);
        ApprovalService::approve($req, $request->comment);
        return back()->with('success', 'Request approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:2000']);
        $req = ApprovalRequest::forBranch($this->branchId())->pending()->findOrFail($id);
        ApprovalService::reject($req, $request->reason);
        return back()->with('success', 'Request rejected.');
    }

    /** Count for badge (used in topbar) */
    public function pendingCount()
    {
        return response()->json([
            'count' => ApprovalRequest::forBranch($this->branchId())->pending()->count(),
        ]);
    }
}