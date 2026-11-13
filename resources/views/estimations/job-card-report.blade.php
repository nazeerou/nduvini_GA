<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Job Cards Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            padding: 0;
        }
        .header-wrap { padding: 3px 20px 4px 30px; }
        .header-image {
            display: block;
            width: 100%;
            height: 80px;
            margin: 4px;
            padding: 2px;
        }
        .content { padding: 16px 22px 18px; }

        .title-bar {
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .title-bar .title {
            font-size: 17px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: -.2px;
        }
        .meta-table {
            width: 100%;
            border: none;
            margin-top: 8px;
        }
        .meta-table td {
            border: none;
            padding: 0;
            font-size: 9.5px;
            color: #475569;
        }
        .meta-table td.left  { text-align: left; font-weight: 600; }
        .meta-table td.center{ text-align: center; color: #64748b; }
        .meta-table td.right { text-align: right; }
        .meta-label {
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-right: 4px;
        }

        /* ---- Summary KPI cards ---- */
        .summary { margin-bottom: 16px; }
        .summary table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 0;
        }
        .summary td {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            vertical-align: top;
            width: 33.33%;
        }
        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #0f172a;
        }
        .summary .emerald .summary-value { color: #047857; }
        .summary .amber   .summary-value { color: #b45309; }

        /* ---- Data table ---- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }
        .data-table thead th {
            background: #f1f5f9;
            color: #1e3a5f;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 9px 7px;
            text-align: left;
            font-weight: 700;
            border-top: 1px solid #cbd5e1;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table thead th.center { text-align: center; }
        .data-table tbody td {
            padding: 7px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #334155;
        }
        .data-table tbody tr:nth-child(even) td { background: #fafbfc; }
        .data-table tbody td.center { text-align: center; }
        .data-table tbody tr:last-child td { border-bottom: none; }

        .pill {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .pill-done      { background: #dcfce7; color: #166534; }
        .pill-pending   { background: #fef3c7; color: #92400e; }
        .pill-cancelled { background: #fee2e2; color: #991b1b; }
        .pill-info      { background: #cffafe; color: #155e75; }

        .job-no {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            font-weight: bold;
            color: #1e3a5f;
        }
        .invoice-no {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            font-weight: bold;
            color: #0e7490;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
            font-style: italic;
        }

        .footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header-wrap">
        <img src="{{ public_path('assets/images/header.png') }}" class="header-image" alt="Header">
    </div>

    <div class="content">

        {{-- ============ Title + meta strip ============ --}}
        <div class="title-bar">
            <div class="title">
                {{ $meta['mode'] === 'vehicle' ? 'Vehicle Job Cards Report' : 'Client Job Cards Report' }}
            </div>
            <table class="meta-table">
                <tr>
                    <td class="left">
                        <span class="meta-label">Branch:</span>{{ $branchName }}
                    </td>
                    <td class="center">
                        
                    </td>
                    <td class="right">
                       <span class="meta-label">{{ ucfirst($meta['mode']) }}:</span>{{ $meta['value'] }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- ============ Summary cards ============ --}}
        <div class="summary">
            <table>
                <tr>
                    <td>
                        <div class="summary-label">Total Job Cards</div>
                        <div class="summary-value">{{ $meta['total'] }}</div>
                    </td>
                    <td class="emerald">
                        <div class="summary-label">Work Completed</div>
                        <div class="summary-value">{{ $meta['completed'] }}</div>
                    </td>
                    <td class="amber">
                        <div class="summary-label">Ongoing Work</div>
                        <div class="summary-value">{{ $meta['ongoing'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ============ Data table ============ --}}
        @if ($sales->count() > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:20px;">#</th>
                        <th style="width:50px;">Job Card #</th>
                        <th style="width:70px;">Details</th>
                        <th style="width:90px;">Milleage</th>
                        <th style="width:70px;">Estimate Date</th>
                        <th style="width:70px;">Delivery Date</th>
                        <th class="center" style="width:70px;">Proforma Invoice</th>
                        <th class="center" style="width:70px;">Work Status</th>
                        <th class="center" style="width:65px;">Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $i => $job)
                        @php
                            $status      = (int) $job->status;
                            $isInvoiced  = !empty($job->invoice_number);
                            $deliverydate        = $job->delivery_date ?? $job->created_date;
                            $displayDeliveryDate = $deliverydate ? \Carbon\Carbon::parse($deliverydate)->format('Y-m-d') : '—';
                            $estimatedate        = $job->created_date;
                            $displayEstimateDate = $estimatedate ? \Carbon\Carbon::parse($estimatedate)->format('Y-m-d') : '—';
                            $clientName  = $job->client_name ?: $job->customer_name;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td><span class="job-no">{{ $job->job_card_no ?: '—' }}</span></td>
                            
                            <td>{{ strtoupper($job->vehicle_reg) }} 
                             <span style="display:block;font-size:8.5px;color:#94a3b8;">{{ strtoupper($job->model) }}</span> 
                             </td>
                             <td>
                                    <span style="display:block;font-size:8.5px;color:#94a3b8;">{{ $job->milleage }}</span>
                            </td>
                            <td>{{ $displayEstimateDate }}</td>
                            <td>{{ $displayDeliveryDate }}</td>

                            {{-- Invoice status --}}
                            <td class="center">
                                @if ($isInvoiced)
                                    <span class="pill pill-done">#{{ str_pad((int) $job->invoice_number, 6, '0', STR_PAD_LEFT) }}</span>
                                @elseif ($status === 1)
                                    <span class="pill pill-pending">Not Invoiced</span>
                                @elseif ($status === 0)
                                    <span class="pill pill-info">Ongoing</span>
                                @else
                                    <span class="pill pill-cancelled">Cancelled</span>
                                @endif
                            </td>

                            {{-- Work status --}}
                            <td class="center">
                                @if ($status === 1)
                                    <span class="pill pill-done">Completed</span>
                                @elseif ($status === 0)
                                    <span class="pill pill-pending">In Progress</span>
                                @else
                                    <span class="pill pill-cancelled">Cancelled</span>
                                @endif
                            </td>

                            {{-- Delivery --}}
                            <td class="center">
                                @if ($status === 1)
                                    <span class="pill pill-done">Delivered</span>
                                @elseif ($status === 0)
                                    <span class="pill pill-pending">Pending</span>
                                @else
                                    <span class="pill pill-cancelled">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty">No job cards found for this {{ strtolower($meta['mode']) }}.</div>
        @endif

        

    </div>

</body>
</html>