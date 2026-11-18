<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Salary Advances</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #9c9c9c; }
        th, td { padding: 6px; text-align: left; }
        th { background: #eee; }
        .branch { text-align: center; }
        h2 { text-align: center; }
        h3 { text-align: center; }
        .total-row { font-weight: bold; background-color: #f0f0f0; }

        /* Signature block */
        .signatures {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .signatures td {
            border: none;
            padding: 8px 20px;
            vertical-align: top;
            width: 50%;
        }
        .sig-title {
            font-weight: bold;
            margin-bottom: 6px;
        }
        .sig-image {
            height: 60px;
            max-width: 180px;
            display: block;
            margin-bottom: 4px;
        }
        .sig-line {
            border-top: 1px dashed #000;
            padding-top: 4px;
            width: 180px;
        }
        .sig-name { font-weight: bold; }
        .sig-role {
            font-size: 11px;
            color: #333;
            font-style: italic;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <h2>NDUVINI AUTO WORKS (NAW)</h2>
    <div class="branch">
        @if(Auth::user()->branch_id == 1)
            HEAD OFFICE : Dar es Salaam
        @elseif (Auth::user()->branch_id == 2)
            BRANCH : TABORA
        @elseif (Auth::user()->branch_id == 3)
            BRANCH : DODOMA
        @endif
    </div>

    <h3>Employee Salary Advances</h3>
    <p style="text-align:center; margin: 0;">
        Period: <strong>{{ $period ?? 'All Period' }}</strong>
    </p>

    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee</th>
                <th>Amount (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount        = 0;
                $requesterName      = null;
                $approverName       = null;
                $requesterSignature = null;
                $approverSignature  = null;
                $requesterRole      = null;
                $approverRole       = null;

                // Resolve a stored signature path to a base64 data URI (dompdf-friendly).
                $sigToDataUri = function ($path) {
                    if (!$path) return null;

                    $candidates = [
                        storage_path('app/public/' . ltrim($path, '/')),
                        public_path(ltrim($path, '/')),
                        public_path('storage/' . ltrim($path, '/')),
                        public_path('uploads/' . ltrim($path, '/')),
                    ];

                    foreach ($candidates as $file) {
                        if (is_file($file)) {
                            $mime = mime_content_type($file) ?: 'image/png';
                            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file));
                        }
                    }
                    return null;
                };
            @endphp

            @foreach($salary_advances as $key => $advance)
                @php
                    $totalAmount += $advance->amount;

                    $rowRequester = $advance->requester_name
                        ?? trim(($advance->requester->fname ?? '') . ' ' . ($advance->requester->lname ?? ''));
                    $rowApprover  = $advance->approver_name
                        ?? trim(($advance->approver->fname ?? '') . ' ' . ($advance->approver->lname ?? ''));

                    $requesterName      = $requesterName      ?: ($rowRequester ?: null);
                    $approverName       = $approverName       ?: ($rowApprover  ?: null);
                    $requesterSignature = $requesterSignature ?: ($advance->requester_signature ?? null);
                    $approverSignature  = $approverSignature  ?: ($advance->approver_signature  ?? null);
                    $requesterRole      = $requesterRole      ?: ($advance->requester_role ?? null);
                    $approverRole       = $approverRole       ?: ($advance->approver_role  ?? null);
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        @if ($advance->employee)
                            {{ $advance->employee->firstname }} {{ $advance->employee->surname }}
                        @else
                            <em>No employee</em>
                        @endif
                    </td>
                    <td>{{ number_format($advance->amount, 2) }}</td>
                </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="2" style="text-align: right;">TOTAL</td>
                <td>{{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Signature block: Left = Requested By, Right = Approved By --}}
    @php
        $requesterSigUri = $sigToDataUri($requesterSignature);
        $approverSigUri  = $sigToDataUri($approverSignature);
    @endphp

    <table class="signatures">
        <tr>
            {{-- Left: Requested By --}}
            <td class="sig-block" style="text-align: left;">
                <div class="sig-title">Requested By:</div>

                @if($requesterSigUri)
                    <img src="{{ $requesterSigUri }}" class="sig-image" alt="Requester Signature">
                @endif

                <div class="sig-line">
                    <div class="sig-name">{{ $requesterName ?: '' }}</div>
                    @if($requesterRole)
                        <div class="sig-role">{{ $requesterRole }}</div>
                    @endif
                </div>
            </td>

            {{-- Right: Approved By --}}
            <td class="sig-block" style="text-align: left;">
                <div class="sig-title">Approved By:</div>

                @if($approverSigUri)
                    <img src="{{ $approverSigUri }}" class="sig-image" alt="Approver Signature">
                @endif

                <div class="sig-line">
                    <div class="sig-name">{{ $approverName ?: '' }}</div>
                    @if($approverRole)
                        <div class="sig-role">{{ $approverRole }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>