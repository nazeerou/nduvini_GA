
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Debtors Report</title>

    <style>
        @page {
            margin: 4px 24px 48px 24px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
        }

        /* Watermark */
        .logo-watermark {
            position: fixed;
            top: 25%;
            left: 22%;
            width: 56%;
            opacity: 0.05;
            z-index: -2;
        }

        .logo-watermark img {
            width: 100%;
        }

        /* Header */
        .header {
            text-align: center;
        }

        .header-image {
            display: block;
            width: 100%;
            height: auto;
            margin-bottom: 8px;
        }

        .company-info {
            font-size: 16px;
            font-weight: bold;
            line-height: 1.5;
            text-transform: uppercase;
        }

        .title {
            margin-top: 8px;
            font-size: 15px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 5px;
            font-size: 11px;
            color: #666;
        }

        .branch {
            text-align: center;
            font-size: 10px;
            padding-top: 5px;
            color: #666;
            text-transform: uppercase;
        }

        /* Report period */
        .report-meta {
            width: 100%;
            border-collapse: collapse;
            margin-top: 17px;
            margin-bottom: 8px;
        }

        .report-meta td {
            padding: 5px 0;
            font-size: 10px;
        }

        .report-meta .period {
            text-align: left;
        }

        .report-meta .generated {
            text-align: right;
        }

        /* Main data table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            /* table-layout: fixed; */
            margin-top: 5px;
            font-size: 9px;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            background: #ededed;
            color: #222;
            border: 1px solid #d8dde0;
            padding: 8px 5px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: left;
        }

        .data-table td {
            border: 1px solid #d0e3f3;
            padding: 7px 5px;
            vertical-align: middle;
            line-height: 1.3;
            word-wrap: break-word;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .category-cell {
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: middle;
            background: #f4f4f4;
        }

        .particulars-cell {
            vertical-align: middle;
        }

        .particulars {
            font-weight: bold;
            text-transform: uppercase;
        }

        .period-text {
            color: #777;
            font-size: 8.5px;
            margin-top: 2px;
        }

        .amount-cell,
        .paid-cell,
        .unpaid-cell {
            text-align: right;
            white-space: nowrap;
        }

        .status-cell {
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .category-start td {
            border-top: 1.2px solid #9bb6be;
        }

        /* Grand totals */
        .data-table tfoot {
            display: table-row-group;
        }

        .data-table tfoot tr {
            page-break-inside: avoid;
        }

        .data-table tfoot td {
            background: #dfebee;
            /* border: 1px solid #aaa; */
            padding: 11px 5px;
            font-weight: bold;
        }

        .grand-label {
            text-align: right;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .empty-cell {
            text-align: center;
            font-style: italic;
            color: #888;
            padding: 20px !important;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>

    {{-- Watermark --}}
    @if(file_exists(public_path('assets/images/logo_nduvini.jpeg')))
        <div class="logo-watermark">
            <img
                src="{{ public_path('assets/images/logo_nduvini.jpeg') }}"
                alt="Company Watermark"
            >
        </div>
    @endif

    {{-- Company header --}}
    <div class="header">

        @if(file_exists(public_path('assets/images/header.png')))
            <img
                src="{{ public_path('assets/images/header.png') }}"
                class="header-image"
                alt="Company Header"
            >
        @endif

        <div class="company-info">
            {{ strtoupper($settings['business_name'] ?? config('app.name')) }}
        </div>

        <div class="title">
            Debtors Report
        </div>

        <div class="subtitle">
            Financial Statement
        </div>

        <div class="branch">
            @if(Auth::user()->branch_id == 1)
                HEAD OFFICE - DAR ES SALAAM
            @elseif(Auth::user()->branch_id == 2)
                TABORA BRANCH
            @elseif(Auth::user()->branch_id == 3)
                DODOMA BRANCH
            @else
                {{ strtoupper($settings['address'] ?? '') }}
            @endif
        </div>

    </div>

    {{-- Reporting period --}}
    <table class="report-meta">
        <tr>
            <td class="period">
                <strong>Period:</strong>

                {{ !empty($filters['month'])
                    ? date('F', mktime(
                        0,
                        0,
                        0,
                        (int) $filters['month'],
                        1
                    ))
                    : 'All' }}

                @if(!empty($filters['year']))
                    / {{ $filters['year'] }}
                @endif
            </td>

            
        </tr>
    </table>

    {{-- Debtors statement --}}
    <table class="data-table">

        <colgroup>
            <col style="width: 17%;">
            <col style="width: 32%;">
            <col style="width: 14%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
            <col style="width: 11%;">
        </colgroup>

        <thead>
            <tr>
                <th>Category</th>
                <th>Particulars</th>
                <th style="text-align: right;">Amount</th>
                <th style="text-align: right;">Paid</th>
                <th style="text-align: right;">Unpaid</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse($groupedDebts as $group)

                @foreach($group as $d)

                    <tr class="{{ $loop->first ? 'category-start' : '' }}">

                        {{-- Category shown once per group --}}
                        @if($loop->first)
                            <td
                                class="category-cell"
                                rowspan="{{ $group->count() }}"
                            >
                                {{ strtoupper($d->category->name ?? 'Uncategorized') }}
                            </td>
                        @endif

                        {{-- Debt particulars --}}
                        <td class="particulars-cell">

                            <div class="particulars">
                                {{ $d->particulars }}
                            </div>

                            @if(!empty($d->period))
                                <div class="period-text">
                                    {{ $d->period }}
                                </div>
                            @endif

                        </td>

                        {{-- Total amount --}}
                        <td class="amount-cell">
                            {{ number_format((float) $d->amount, 2) }}
                        </td>

                        {{-- Amount paid --}}
                        <td class="paid-cell">
                            {{ number_format((float) $d->paid_amount, 2) }}
                        </td>

                        {{-- Outstanding amount --}}
                        <td class="unpaid-cell">
                            {{ number_format((float) $d->unpaid_amount, 2) }}
                        </td>

                        {{-- Payment status --}}
                        <td class="status-cell">
                            {{ strtoupper($d->status ?? 'PENDING') }}
                        </td>

                    </tr>

                @endforeach

            @empty

                <tr>
                    <td colspan="6" class="empty-cell">
                        No debt records found for the selected filters.
                    </td>
                </tr>

            @endforelse

        </tbody>

        {{-- Financial totals --}}
        @if($debts->isNotEmpty())

            <tfoot>
                <tr>

                    <td colspan="2" class="grand-label">
                        Grand Total
                    </td>

                    <td class="amount-cell">
                        {{ number_format((float) $summary['total'], 2) }}
                    </td>

                    <td class="paid-cell">
                        {{ number_format((float) $summary['paid'], 2) }}
                    </td>

                    <td class="unpaid-cell">
                        {{ number_format((float) $summary['unpaid'], 2) }}
                    </td>

                    <td></td>

                </tr>
            </tfoot>

        @endif

    </table>

    {{-- Footer --}}
    <div class="footer">
        Generated on: {{ now()->format('d F Y') }}
        &nbsp; | &nbsp;
        Printed by:
        {{ Auth::user()->fname }} {{ Auth::user()->lname }}
    </div>

    {{-- Page numbering for Dompdf --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("DejaVu Sans", "normal");
            $size = 8;

            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";

            $width = $fontMetrics->getTextWidth(
                "Page 00 of 00",
                $font,
                $size
            );

            $pdf->page_text(
                545 - $width,
                815,
                $text,
                $font,
                $size,
                array(120, 120, 120)
            );
        }
    </script>

</body>
</html>
