
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vehicle Services Report</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #334155;
            padding: 0;
        }

        .header-wrap {
            padding: 3px 20px 4px 30px;
        }

        .header-image {
            display: block;
            width: 100%;
            height: 80px;
            margin: 4px;
            padding: 2px;
        }

        .content {
            padding: 16px 22px 18px;
        }

        /* REPORT HEADING */
        .title-bar {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 17px;
        }

        .title {
            font-size: 17px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 8px;
            color: #64748b;
            margin-top: 4px;
            letter-spacing: .5px;
        }

        .meta-table {
            width: 100%;
            border: none;
            margin-top: 12px;
        }

        .meta-table td {
            border: none;
            padding: 3px 0;
            font-size: 9px;
            color: #475569;
        }

        .meta-table td.left {
            text-align: left;
        }

        .meta-table td.center {
            text-align: center;
        }

        .meta-table td.right {
            text-align: right;
        }

        .meta-label {
            text-transform: uppercase;
            font-size: 7.5px;
            letter-spacing: .5px;
            color: #94a3b8;
            margin-right: 4px;
            font-weight: bold;
        }

        /* SUMMARY */
        .summary {
            margin-bottom: 18px;
        }

        .summary table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
        }

        .summary td {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 11px 12px;
            vertical-align: middle;
            width: 33.33%;
        }

        .summary-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .7px;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .summary .emerald .summary-value {
            color: #047857;
        }

        .summary .amber .summary-value {
            color: #b45309;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table thead th {
            background: #eef2f7;
            color: #1e3a5f;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 10px 6px;
            text-align: left;
            font-weight: bold;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }

        .data-table thead th.center {
            text-align: center;
        }

        .data-table tbody tr {
            page-break-inside: avoid;
        }

        .data-table tbody td {
            padding: 9px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #334155;
        }

        .data-table tbody tr:nth-child(even) td {
            background: #fafbfc;
        }

        .data-table tbody td.center {
            text-align: center;
        }

        .data-table tbody tr:last-child td {
            border-bottom: 1px solid #cbd5e1;
        }

        .number {
            color: #94a3b8;
            font-size: 8px;
            text-align: center;
        }

        .invoice-no {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8px;
            font-weight: bold;
            color: #1e3a5f;
        }

        .vehicle-reg {
            font-weight: bold;
            color: #1e3a5f;
            font-size: 9px;
        }

        .vehicle-model {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 3px;
        }

        .mileage {
            font-weight: bold;
            color: #334155;
            white-space: nowrap;
        }

        .job-card {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 8px;
            color: #334155;
            font-weight: bold;
        }

        /* STATUS */
        .pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .pill-done {
            background: #dcfce7;
            color: #166534;
        }

        .pill-pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* EMPTY */
        .empty {
            text-align: center;
            padding: 40px 10px;
            color: #94a3b8;
            font-style: italic;
            border: 1px solid #e2e8f0;
        }

        /* FOOTER */
        .footer {
            margin-top: 18px;
            padding-top: 9px;
            border-top: 1px solid #e2e8f0;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
        }

    </style>
</head>

<body>

    {{-- COMPANY HEADER --}}
    <div class="header-wrap">
        <img
            src="{{ public_path('assets/images/header.png') }}"
            class="header-image"
            alt="Company Header"
        >
    </div>

    <div class="content">

        {{-- REPORT TITLE --}}
        <div class="title-bar">

            <div class="title">
                {{ $mode === 'client' ? 'Proforma Report' : 'Vehicle Services Report' }}
            </div>

            <div class="subtitle">
                VEHICLE RECORDS & JOB CARD TRACKING
            </div>

            <table class="meta-table">
                <tr>
                    <td class="left">
                        <span class="meta-label">Branch:</span>
                        {{ $branchName }}
                    </td>

                    <td class="center">
                        <span class="meta-label">{{ $modeLabel }}:</span>
                        {{ $value }}
                    </td>
                    <td class="right"></div>
                </tr>
            </table>

        </div>

        @php
            $total = $rows->count();

            $done = $rows->filter(function ($r) {
                return !empty($r->job_card_no);
            })->count();

            $pending = $total - $done;
        @endphp

        {{-- SUMMARY CARDS --}}
        <div class="summary">
            <table>
                <tr>
                    <td>
                        <div class="summary-label">
                            Total Vehicles / Records
                        </div>
                        <div class="summary-value">
                            {{ number_format($total) }}
                        </div>
                    </td>

                    <td class="emerald">
                        <div class="summary-label">
                            Job Cards Issued
                        </div>
                        <div class="summary-value">
                            {{ number_format($done) }}
                        </div>
                    </td>

                    <td class="amber">
                        <div class="summary-label">
                            Pending Job Cards
                        </div>
                        <div class="summary-value">
                            {{ number_format($pending) }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- VEHICLE RECORDS --}}
        @if($total > 0)

            <table class="data-table">

                <thead>
                    <tr>
                        <th style="width: 5%;" class="center">#</th>

                        <th style="width: 17%;">
                            Proforma No.
                        </th>

                        <th style="width: 23%;">
                            Vehicle Reg#
                        </th>

                        <th style="width: 13%;">
                            Mileage
                        </th>

                        <th style="width: 16%;">
                            Estimated Date
                        </th>

                        <th style="width: 14%;" class="center">
                            Job Card No.
                        </th>

                        <th style="width: 12%;" class="center">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($rows as $i => $row)

                        @php
                            $hasJob = !empty($row->job_card_no);
                        @endphp

                        <tr>

                            <td class="number">
                                {{ $i + 1 }}
                            </td>

                            <td>
                                <span class="invoice-no">
                                    {{ $row->profoma_invoice ?: $row->reference }}
                                </span>
                            </td>

                            <td>
                                <div class="vehicle-reg">
                                    {{ strtoupper($row->vehicle_reg ?? 'N/A') }}
                                </div>

                                <div class="vehicle-model">
                                    {{ strtoupper($row->model ?? '') }}
                                </div>
                            </td>

                            <td>
                                <span class="mileage">
                                    {{ is_numeric($row->mileage ?? null)
                                        ? number_format($row->mileage) . ' KM'
                                        : ($row->mileage ?? '—') }}
                                </span>
                            </td>

                            <td>
                                {{ $row->created_date }}
                            </td>

                            <td class="center">
                                <span class="job-card">
                                    {{ $hasJob ? 'JC-' . $row->job_card_no : '—' }}
                                </span>
                            </td>

                            <td class="center">
                                <span class="pill {{ $hasJob ? 'pill-done' : 'pill-pending' }}">
                                    {{ $hasJob ? 'Issued' : 'Pending' }}
                                </span>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="empty">
                No vehicle records found for this {{ strtolower($modeLabel) }}.
            </div>

        @endif

        {{-- FOOTER --}}
        <div class="footer">
            Generated on : {{ $generatedAt }}
        </div>

    </div>

</body>
</html>
