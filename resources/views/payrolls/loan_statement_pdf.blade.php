<!DOCTYPE html>
<html>
<head>
<title>Loan Statement</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; padding: 24px; color: #0f172a; font-size: 12px; }
    .header { text-align: center; border-bottom: 3px solid #1e293b; padding-bottom: 16px; margin-bottom: 20px; }
    .header h1 { margin: 0 0 6px; font-size: 22px; color: #0f172a; letter-spacing: .5px; }
    .header h2 { margin: 0 0 4px; font-size: 14px; color: #475569; text-transform: uppercase; letter-spacing: 2px; font-weight: 600; }
    .header p { margin: 0; font-size: 10.5px; color: #94a3b8; }
    
    .info-row { display: flex; justify-content: space-between; margin-bottom: 20px; }
    .info-block { width: 48%; }
    .info-block label { display: block; font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: .8px; font-weight: 700; margin-bottom: 4px; }
    .info-block value { display: block; font-size: 13px; color: #0f172a; font-weight: 700; }
    
    .stats-grid { display: table; width: 100%; margin-bottom: 20px; border-collapse: separate; border-spacing: 4px 0; }
    .stat-box { display: table-cell; width: 25%; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; text-align: center; vertical-align: middle; }
    .stat-box .lbl { font-size: 8.5px; color: #64748b; text-transform: uppercase; letter-spacing: .8px; font-weight: 700; margin-bottom: 4px; }
    .stat-box .val { font-family: 'DejaVu Sans Mono', monospace; font-size: 14px; color: #0f172a; font-weight: 700; }
    .stat-box.accent .val { color: #b45309; }
    .stat-box.success .val { color: #334155; }
    
    .progress-section { margin-bottom: 20px; }
    .progress-label { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 10px; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 700; }
    .progress-label strong { color: #1e293b; font-size: 11px; }
    .progress-track { height: 8px; background: #f1f5f9; border-radius: 999px; overflow: hidden; }
    .progress-fill { height: 100%; background: linear-gradient(90deg, #334155, #64748b); border-radius: 999px; }
    
    .section-title { font-size: 13px; font-weight: 700; color: #0f172a; margin: 24px 0 12px; padding-bottom: 6px; border-bottom: 2px solid #e2e8f0; display: flex; justify-content: space-between; }
    .section-title span { font-size: 10px; color: #94a3b8; font-weight: 500; }
    
    .repay-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .repay-table thead th { background: #f1f5f9; color: #475569; font-size: 9.5px; text-transform: uppercase; letter-spacing: .6px; padding: 8px 10px; text-align: left; border-bottom: 2px solid #e2e8f0; }
    .repay-table thead th.right { text-align: right; }
    .repay-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; color: #334155; }
    .repay-table tbody td.right { text-align: right; font-family: 'DejaVu Sans Mono', monospace; font-weight: 700; }
    .repay-table tbody td.mono { font-family: 'DejaVu Sans Mono', monospace; }
    .repay-table tbody td.amount { color: #334155; font-weight: 700; text-align: right; font-family: 'DejaVu Sans Mono', monospace; }
    .repay-table tbody tr:last-child td { border-bottom: 2px solid #e2e8f0; }
    .repay-table tbody tr.empty td { text-align: center; color: #94a3b8; font-style: italic; padding: 20px; }
    
    .no-repayments { text-align: center; padding: 24px; background: #f8fafc; border-radius: 8px; color: #94a3b8; font-style: italic; font-size: 11px; }
    
    .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; text-align: center; }
    .footer strong { color: #475569; }
</style>
</head>
<body>

<div class="header">
    <h1>NDUVINI AUTOWORKS (NAW)</h1>
    <h2>Employee Loan Repayment Statement</h2>
    <p>Generated on {{ now()->format('d F Y, H:i') }}</p>
</div>

<!-- Info -->
<div class="info-row">
    <div class="info-block">
        <label>Employee</label>
        <value>{{ trim((optional($loanModel->employee)->firstname ?? '') . ' ' . (optional($loanModel->employee)->surname ?? '')) ?: 'N/A' }}</value>
        <div style="margin-top: 8px;">
            <label>Employee No.</label>
            <value>#{{ optional($loanModel->employee)->id ?? '—' }}</value>
        </div>
    </div>
    <div class="info-block">
        <label>Loan Type</label>
        <value style="text-transform: capitalize;">{{ $loanModel->type ?? 'Loan' }}</value>
        <div style="margin-top: 8px;">
            <label>Start Date</label>
            <value>{{ $loanModel->start_date ? \Carbon\Carbon::parse($loanModel->start_date)->format('d M Y') : '—' }}</value>
        </div>
    </div>
</div>

<!-- Stats -->
<table class="stats-grid" style="border-collapse: separate; border-spacing: 4px 0;">
    <tr>
        <td class="stat-box">
            <div class="lbl">Principal</div>
            <div class="val">TZS {{ number_format($loanModel->principal, 2) }}</div>
        </td>
        <td class="stat-box success">
            <div class="lbl">Total Repaid</div>
            <div class="val">TZS {{ number_format($totalRepaid, 2) }}</div>
        </td>
        <td class="stat-box accent">
            <div class="lbl">Outstanding</div>
            <div class="val">TZS {{ number_format($balance, 2) }}</div>
        </td>
        <td class="stat-box">
            <div class="lbl">Monthly Deduction</div>
            <div class="val">TZS {{ number_format($loanModel->monthly_deduction ?? 0, 2) }}</div>
        </td>
    </tr>
</table>

<!-- Progress -->
<div class="progress-section">
    <div class="progress-label">
        <span>Repayment Progress</span>
        <strong>{{ number_format($progress, 1) }}%</strong>
    </div>
    <div class="progress-track">
        <div class="progress-fill" style="width: {{ $progress }}%;"></div>
    </div>
</div>

<!-- Repayment History -->
<div class="section-title">
    <span>Repayment History</span>
    <span>{{ $repayments->count() }} payment{{ $repayments->count() !== 1 ? 's' : '' }}</span>
</div>

@if($repayments->count() > 0)
    <table class="repay-table">
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Date</th>
                <th>Notes</th>
                <th class="right">Amount (TZS)</th>
                <th class="right">Running Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $runningBalance = (float) $loanModel->principal;
                $sortedRepayments = $repayments->sortBy('repayment_date');
            @endphp
            @foreach($sortedRepayments as $i => $r)
                @php
                    $runningBalance -= (float) $r->amount;
                    $runningBalance = max(0, $runningBalance);
                @endphp
                <tr>
                    <td class="mono">{{ $i + 1 }}</td>
                    <td>{{ $r->repayment_date ? \Carbon\Carbon::parse($r->repayment_date)->format('d M Y') : ($r->created_at ? $r->created_at->format('d M Y') : '—') }}</td>
                    <td style="color:#64748b;">{{ $r->notes ?: '—' }}</td>
                    <td class="amount">{{ number_format($r->amount, 2) }}</td>
                    <td class="right">{{ number_format($runningBalance, 2) }}</td>
                </tr>
            @endforeach
            <tr style="background: #f8fafc;">
                <td colspan="3" style="text-align: right; font-weight: 700; color: #475569; text-transform: uppercase; font-size: 10px; letter-spacing: .5px;">Total Repaid</td>
                <td class="amount" style="font-size: 13px;">{{ number_format($totalRepaid, 2) }}</td>
                <td class="right" style="font-size: 13px; color: #b45309;">{{ number_format($balance, 2) }}</td>
            </tr>
        </tbody>
    </table>
@else
    <div class="no-repayments">No repayments have been recorded for this loan yet.</div>
@endif

<div class="footer">
    <p><strong>NDUVINI AUTOWORKS (NAW)</strong> — Employee Loan Repayment Statement</p>
    <p>This is a system-generated document. For inquiries, contact your HR/payroll department.</p>
</div>

</body>
</html>