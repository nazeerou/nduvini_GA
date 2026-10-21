<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee List</title>
    <style>
        @page { margin: 15px 20px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #333;
        }

        /* ---------- HEADER ---------- */
        .header {
            text-align: center;
            border-bottom: 3px solid #4e54c8;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header-image {
            display: block;
            width: 100%;          /* stretch to full landscape content width */
            height: auto;
            max-height: 110px;    /* cap height so the table still fits */
            margin: 0 0 10px 0;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #4e54c8;
            text-align: center;
            letter-spacing: .5px;
        }

        .header .sub {
            margin-top: 6px;
            font-size: 11px;
            color: #555;
            text-align: center;
        }
        .header .sub strong { color: #4e54c8; }

        /* ---------- META ---------- */
        .meta {
            display: flex;
            justify-content: space-between;
            font-size: 9.5px;
            color: #666;
            margin-bottom: 8px;
        }

        /* ---------- TABLE ---------- */
        table { width: 100%; border-collapse: collapse; }

        th, td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background: #4e54c8;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        tbody tr:nth-child(even) { background: #f8f9fa; }

        .empty {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: -12px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
        }
    </style>
</head>
<body>

    <div class="header">
        @php
            $headerPath = public_path('assets/images/header.png');
        @endphp

        @if(file_exists($headerPath))
            <img
                src="{{ $headerPath }}"
                class="header-image"
                alt="Company Header"
            >
        @endif

        <h1>Employee Report</h1>

        <div class="sub">
            Branch: <strong>{{ $branchName }}</strong>
            &nbsp;•&nbsp;
            Contract Type: <strong>{{ $contractName }}</strong>
            &nbsp;•&nbsp;
            Total: <strong>{{ count($employees) }}</strong>
        </div>
    </div>

    <div class="meta">
        <span>Generated: {{ $generatedAt }}</span>
        <span>By: {{ $generatedBy }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:35px;">#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Contract</th>
                <th>Branch</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $i => $emp)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ trim($emp->firstname . ' ' . $emp->middlename . ' ' . $emp->surname) }}</td>
                    <td>{{ $emp->email ?? '—' }}</td>
                    <td>{{ $emp->mobile ?? '—' }}</td>
                    <td>{{ $emp->department_name ?? '—' }}</td>
                    <td>{{ $emp->position ?? '—' }}</td>
                    <td>{{ $emp->contract_name ?? '—' }}</td>
                    <td>{{ $emp->branch_name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="empty">No employees found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated from Employee Management System — {{ date('Y') }}
    </div>
</body>
</html>