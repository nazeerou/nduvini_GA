@extends('layouts.app_header')
@section('page-title', 'HR Approval Inbox')
@section('content')

<style>
    .appr-wrap { background: #f1f5f9; padding: 4px; }
    .appr-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        box-shadow: 0 1px 3px rgba(15,23,42,.05);
        overflow: hidden;
    }
    .appr-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 18px 24px; border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(180deg, #fff, #f8fafc);
    }
    .appr-card-head h3 {
        margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;
        display: flex; align-items: center; gap: 10px;
    }
    .appr-card-head h3 i { color: #2563eb; }
    .appr-card-head .count-pill {
        background: #2563eb; color: #fff; padding: 3px 10px;
        border-radius: 999px; font-size: 11px; font-weight: 700;
    }
    .appr-card-body { padding: 0; }

    .appr-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
        transition: background .15s ease;
    }
    .appr-row:last-child { border-bottom: none; }
    .appr-row:hover { background: #f8fafc; }

    .appr-row-title { font-weight: 700; color: #0f172a; font-size: 14px; margin: 0 0 4px; }
    .appr-row-meta  { font-size: 12px; color: #64748b; display: flex; flex-wrap: wrap; gap: 12px; }
    .appr-row-meta span { display: inline-flex; align-items: center; gap: 5px; }
    .appr-row-amount {
        font-family: 'SFMono-Regular', Consolas, monospace;
        font-size: 15px; font-weight: 700; color: #0f172a;
        margin-left: 4px;
    }

    .appr-row-actions { display: flex; gap: 6px; }
    .appr-row-actions .btn { font-size: 12px; padding: 7px 13px; border-radius: 6px; font-weight: 600; }

    .appr-section-title {
        padding: 14px 24px; font-size: 12px; text-transform: uppercase;
        letter-spacing: .07em; color: #64748b; font-weight: 700;
        border-bottom: 1px solid #f1f5f9; background: #f8fafc;
    }

    .appr-empty { padding: 40px 24px; text-align: center; color: #94a3b8; }
    .appr-empty i { font-size: 36px; margin-bottom: 10px; display: block; color: #cbd5e1; }

    /* Status badges */
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
    .status-pending  { background: #fef3c7; color: #92400e; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-paid     { background: #dbeafe; color: #1e40af; }
</style>

<div class="appr-wrap">
    <div class="row"><div class="col-md-12">
        <div class="step-indicator" style="float:right; margin-bottom:14px;">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="#">HR Approvals</a>
        </div>
    </div></div>

    {{-- Pending --}}
    <div class="appr-card" style="margin-bottom:20px;">
        <div class="appr-card-head">
            <h3><i class="fa fa-hourglass-half"></i> Awaiting Your Review
                <span class="count-pill">{{ $pending->count() }}</span>
            </h3>
        </div>
        <div class="appr-card-body">
            @forelse($pending as $req)
                <div class="appr-row">
                    <div>
                        <p class="appr-row-title">
                            {{ $req->subject }}
                            <span class="status-badge status-pending" style="margin-left:8px;">
                                <i class="fa fa-clock-o"></i> Pending
                            </span>
                        </p>
                        <div class="appr-row-meta">
                            <span><i class="fa fa-user"></i> {{ $req->requester->fname ?? '' }} {{ $req->requester->lname ?? '' }}</span>
                            <span><i class="fa fa-clock-o"></i> {{ $req->requested_at?->diffForHumans() }}</span>
                            <span><i class="fa fa-tag"></i> {{ $req->reference_no }}</span>
                            <span class="appr-row-amount">TZS {{ number_format($req->amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="appr-row-actions">
                        <button class="btn btn-info" onclick="openApprovalDetails({{ $req->id }})">
                            <i class="fa fa-eye"></i> Details
                        </button>
                        <form action="{{ route('approvals.approve', $req->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-success"
                                    onclick="return confirm('Approve this request?')">
                                <i class="fa fa-check"></i> Approve
                            </button>
                        </form>
                        <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal{{ $req->id }}">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    </div>
                </div>

                {{-- Reject modal per request --}}
                <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <form class="modal-content" method="POST" action="{{ route('approvals.reject', $req->id) }}">
                            @csrf
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title"><i class="fa fa-times-circle" style="color:#dc2626;"></i> Reject Request</h4>
                            </div>
                            <div class="modal-body">
                                <p>You are rejecting <strong>{{ $req->subject }}</strong>.</p>
                                <div class="form-group">
                                    <label>Reason for Rejection <span class="text-danger">*</span></label>
                                    <textarea name="reason" class="form-control" rows="3" required
                                              placeholder="Explain why this request is being rejected..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="appr-empty">
                    <i class="fa fa-inbox"></i>
                    <p>No pending requests.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recently Approved --}}
    <div class="appr-card" style="margin-bottom:20px;">
        <div class="appr-card-head">
            <h3><i class="fa fa-check-circle" style="color:#059669;"></i> Recently Approved</h3>
        </div>
        <div class="appr-card-body">
            @forelse($approved as $req)
                <div class="appr-row">
                    <div>
                        <p class="appr-row-title">{{ $req->subject }}
                            <span class="status-badge status-approved" style="margin-left:8px;"><i class="fa fa-check"></i> Approved</span>
                        </p>
                        <div class="appr-row-meta">
                            <span><i class="fa fa-user"></i> By {{ $req->reviewer->fname ?? 'HR' }}</span>
                            <span><i class="fa fa-clock-o"></i> {{ $req->reviewed_at?->diffForHumans() }}</span>
                            <span class="appr-row-amount">TZS {{ number_format($req->amount, 2) }}</span>
                        </div>
                    </div>
                    <div class="appr-row-actions">
                        <button class="btn btn-info btn-sm" onclick="openApprovalDetails({{ $req->id }})">
                            <i class="fa fa-eye"></i> Details
                        </button>
                    </div>
                </div>
            @empty
                <div class="appr-empty"><i class="fa fa-check-circle"></i><p>Nothing yet.</p></div>
            @endforelse
        </div>
    </div>

    {{-- Recently Rejected --}}
    <div class="appr-card">
        <div class="appr-card-head">
            <h3><i class="fa fa-times-circle" style="color:#dc2626;"></i> Recently Rejected</h3>
        </div>
        <div class="appr-card-body">
            @forelse($rejected as $req)
                <div class="appr-row">
                    <div>
                        <p class="appr-row-title">{{ $req->subject }}
                            <span class="status-badge status-rejected" style="margin-left:8px;"><i class="fa fa-times"></i> Rejected</span>
                        </p>
                        <div class="appr-row-meta">
                            <span><i class="fa fa-user"></i> By {{ $req->reviewer->fname ?? 'HR' }}</span>
                            <span><i class="fa fa-clock-o"></i> {{ $req->reviewed_at?->diffForHumans() }}</span>
                            <span class="appr-row-amount">TZS {{ number_format($req->amount, 2) }}</span>
                        </div>
                        @if($req->rejection_reason)
                            <p style="font-size:12.5px; color:#991b1b; margin-top:6px; font-style:italic;">
                                <i class="fa fa-quote-left"></i> {{ $req->rejection_reason }}
                            </p>
                        @endif
                    </div>
                    <div class="appr-row-actions">
                        <button class="btn btn-info btn-sm" onclick="openApprovalDetails({{ $req->id }})">
                            <i class="fa fa-eye"></i> Details
                        </button>
                    </div>
                </div>
            @empty
                <div class="appr-empty"><i class="fa fa-times-circle"></i><p>Nothing yet.</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Details Modal --}}
<div class="modal fade" id="approvalDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Approval Request Details</h4>
            </div>
            <div class="modal-body" id="approvalDetailsBody">
                <div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<script>
function openApprovalDetails(id) {
    var $body = $('#approvalDetailsBody');
    $body.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
    $('#approvalDetailsModal').modal('show');

    $.get('{{ url("approvals") }}/' + id, function (res) {
        $body.html(res.html);
    }).fail(function () {
        $body.html('<div class="alert alert-danger">Failed to load details.</div>');
    });
}
</script>

@endsection