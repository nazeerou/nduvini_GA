<style>
    .loan-statement {
        font-family: Arial, sans-serif;
        font-size: 13px;
        color: #333;
        padding: 10px;
    }

    .loan-statement h2.title {
        text-align: center;
        margin-bottom: 20px;
        text-transform: uppercase;
        font-weight: bold;
        color: #000;
    }

    .loan-info-table {
        width: 100%;
        margin-bottom: 20px;
        border-collapse: collapse;
    }

    .loan-info-table td {
        padding: 8px 10px;
        border: 1px solid #ddd;
    }

    .repayment-table {
        width: 100%;
        border-collapse: collapse;
    }

    .repayment-table th,
    .repayment-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    .repayment-table th {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .text-muted {
        color: #777;
        font-style: italic;
    }
    @media print {
    .no-print {
        display: none !important;
    }
}
</style>

<div class="loan-statement">
    <table class="loan-info-table">
        <tr>
            <td><strong>Employee:</strong></td>
            <td>{{ $loan->employee->firstname }} {{ $loan->employee->surname }}</td>
        </tr>
        <tr>
            <td><strong>Loan Type:</strong></td>
            <td>{{ $loan->type }}</td>
        </tr>
        <tr>
            <td><strong>Loan Amount:</strong></td>
            <td>{{ number_format($loan->principal, 2) }} TZS</td>
        </tr>
        <tr>
            <td><strong>Monthly Deduction:</strong></td>
            <td>{{ number_format($loan->monthly_deduction, 2) }} TZS</td>
        </tr>
        <tr>
            <td><strong>Outstanding Balance:</strong></td>
            <td>{{ number_format($loan->balance, 2) }} TZS</td>
        </tr>
        <tr>
            <td><strong>Start Date:</strong></td>
            <td>{{ \Carbon\Carbon::parse($loan->start_date)->format('F d, Y') }}</td>
        </tr>
    </table>

    <h4 style="margin-top: 30px;">Repayment History</h4>

    @if($loan->loanRepayments->count() > 0)
        <table class="repayment-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Date</th>
                    <th>Amount Paid (TZS)</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php $totalPaid = 0; @endphp
                @foreach($loan->loanRepayments as $index => $repayment)
                    @php $totalPaid += $repayment->amount; @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($repayment->payment_date)->format('d M Y') }}</td>
                        <td>{{ number_format($repayment->amount, 2) }}</td>
                        <td>{{ $repayment->remarks ?? 'PAID' }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="2">Total Paid</td>
                    <td>{{ number_format($totalPaid, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="text-muted">No repayments found.</p>
    @endif
</div>
