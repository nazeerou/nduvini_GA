<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            padding: 0;
        }

        /* ---------- Header image with inset ---------- */
        .header-wrap{
            padding: 3px 20px 4px 30px;
        }
        .header-image{
            display: block;
            width: 100%;
            height: 70px;
            margin: 4px;
            padding: 2px;
        }

        /* ---------- Padded content area ---------- */
        .pdf-content{
            padding: 16px 22px 18px;
        }

        /* ---------- Centered title + meta row ---------- */
        .pdf-title-bar{
            text-align: center;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .pdf-title{
            font-size: 17px;
            font-weight: bold;
            color: #1e3a5f;
            letter-spacing: -.2px;
        }
        .pdf-meta-table{
            width: 100%;
            border: none;
            margin-top: 8px;
        }
        .pdf-meta-table td{
            border: none;
            padding: 0;
            font-size: 9.5px;
            color: #475569;
            vertical-align: middle;
        }
        .pdf-meta-table td.left{text-align: left; font-weight: 600;}
        .pdf-meta-table td.right{text-align: right;}
        .pdf-meta-label{
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-right: 4px;
        }

        /* ---------- Summary strip ---------- */
        .pdf-summary{
            margin-bottom:16px;
        }
        .pdf-summary table{width:100%;border-collapse:separate;border-spacing:6px 0;}
        .pdf-summary td{
            background:#f8fafc;
            border:1px solid #e2e8f0;
            border-radius:4px;
            padding:10px 12px;
            width:50%;
            vertical-align:top;
        }
        .pdf-summary-label{
            font-size:8px;
            text-transform:uppercase;
            letter-spacing:.08em;
            color:#64748b;
            font-weight:bold;
            margin-bottom:4px;
        }
        .pdf-summary-value{
            font-size:13px;
            font-weight:bold;
            color:#0f172a;
        }
        .pdf-summary .paid .pdf-summary-value{color:#059669;}
        .pdf-summary .partial .pdf-summary-value{color:#d97706;}
        .pdf-summary .unpaid .pdf-summary-value{color:#dc2626;}

        /* ---------- Data table (neutral / professional) ---------- */
        .pdf-table{
            width:100%;
            border-collapse:collapse;
            font-size:9.5px;
        }
        .pdf-table thead th{
            background:#f1f5f9;
            color:#1e3a5f;
            font-size:8.5px;
            text-transform:uppercase;
            letter-spacing:.06em;
            padding:9px 7px;
            text-align:left;
            font-weight:700;
            border-top:1px solid #cbd5e1;
            border-bottom:1px solid #cbd5e1;
        }
        .pdf-table thead th.right{text-align:right;}
        .pdf-table thead th.center{text-align:center;}
        .pdf-table tbody td{
            padding:7px;
            border-bottom:1px solid #e2e8f0;
            vertical-align:middle;
            color:#334155;
        }
        .pdf-table tbody tr:nth-child(even) td{background:#fafbfc;}
        .pdf-table td.right{text-align:right;font-family:'DejaVu Sans Mono', monospace;}
        .pdf-table td.center{text-align:center;}

        /* Status accent (only on the first cell, thin) */
        .pdf-table tbody tr.paid td:first-child{border-left:3px solid #059669;}
        .pdf-table tbody tr.partial td:first-child{border-left:3px solid #d97706;}
        .pdf-table tbody tr.unpaid td:first-child{border-left:3px solid #dc2626;}

        /* Neutral footer row */
        .pdf-table tfoot td{
            padding:10px 7px;
            background:#f1f5f9;
            color:#1e3a5f;
            font-weight:700;
            font-size:9.5px;
            border-top:2px solid #cbd5e1;
            border-bottom:1px solid #cbd5e1;
        }
        .pdf-table tfoot td.right{
            text-align:right;
            font-family:'DejaVu Sans Mono', monospace;
        }

        /* ---------- Status pills ---------- */
        .pill{
            display:inline-block;
            padding:2px 8px;
            border-radius:10px;
            font-size:8px;
            font-weight:bold;
            text-transform:uppercase;
            letter-spacing:.04em;
        }
        .pill-paid{background:#dcfce7;color:#166534;}
        .pill-partial{background:#fef3c7;color:#92400e;}
        .pill-unpaid{background:#fee2e2;color:#991b1b;}

        .pdf-empty{
            text-align:center;
            padding:40px;
            color:#94a3b8;
            font-style:italic;
        }
    </style>
</head>
<body>

    {{-- Header image --}}
    <div class="header-wrap">
        <img src="{{ public_path('assets/images/header.png') }}"
             class="header-image"
             alt="Header">
    </div>

    <div class="pdf-content">

        {{-- Centered title + meta row --}}
        <div class="pdf-title-bar">
            <div class="pdf-title">{{ $title }}</div>

            <table class="pdf-meta-table">
                <tr>
                    <td class="left">
                        <span class="pdf-meta-label">Branch: {{ $branchName->branch_name }} </span>
                    </td>
                    <td class="right">
                        <span class="pdf-meta-label">Generated:</span>{{ $generatedAt }}
                    </td>
                </tr>
            </table>
        </div>

        {{-- Summary --}}
        <div class="pdf-summary">
            <table>
                <tr>
                    <td>
                        <div class="pdf-summary-label">Invoices</div>
                        <div class="pdf-summary-value">{{ $sales->count() }}</div>
                    </td>
                    <td class="paid">
                        <div class="pdf-summary-label">Total Paid</div>
                        <div class="pdf-summary-value">{{ number_format($totals['paid'], 2) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        @if($sales->count() > 0)
        <table class="pdf-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Client Name</th>
                    <th>Vehicle</th>
                    <th>Invoice No</th>
                    <th class="right">Invoice</th>
                    <th class="right">Paid</th>
                    <th class="right">Balance</th>
                    <th>Date</th>
                    <th class="center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $i => $s)
                    @php
                        $bill = (float) $s->bill_amount;
                        $paid = (float) $s->paid_amount;
                        $balance = max(0, $bill - $paid);
                        if ($paid >= $bill) { $st = 'paid'; $stLabel = 'Paid'; }
                        elseif ($paid > 0) { $st = 'partial'; $stLabel = 'Partial'; }
                        else { $st = 'unpaid'; $stLabel = 'Unpaid'; }
                    @endphp
                    <tr class="{{ $st }}">
                        <td>{{ $i + 1 }}</td>
                        <td><strong>{{ strtoupper($s->client_name) }}</strong></td>
                        <td>{{ strtoupper($s->vehicle_reg) }} | {{ strtoupper($s->model) }}</td>
                        <td>{{ $s->invoice_number }}</td>
                        <td class="right">{{ number_format($bill, 2) }}</td>
                        <td class="right">{{ number_format($paid, 2) }}</td>
                        <td class="right">{{ number_format($balance, 2) }}</td>
                        <td>{{ $s->created_date }}</td>
                        <td class="center"><span class="pill pill-{{ $st }}">{{ $stLabel }}</span></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right;">TOTALS</td>
                    <td class="right">{{ number_format($totals['bill'], 2) }}</td>
                    <td class="right">{{ number_format($totals['paid'], 2) }}</td>
                    <td class="right">{{ number_format($totals['balance'], 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="pdf-empty">No invoices found for this filter.</div>
        @endif

    </div>

</body>
</html>