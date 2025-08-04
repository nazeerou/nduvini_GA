<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Supplier Statement') }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        #sales_report td, #sales_report th {
            border: 1px solid #ddd;
            padding: 6px 8px;
        }
        #sales_report th {
            background-color: #eee;
        }
        #sales_report tr:hover {
            background-color: #ddd;
        }
        header {
            position: fixed;
            top: -136px;
            width: 100%;
            height: 130px;
        }
        footer {
            position: fixed;
            bottom: -95px;
            height: 90px;
            width: 100%;
            text-align: right;
            font-size: 13px;
        }
        .page-number:before {
            content: counter(page);
        }
        main {
            margin-top: 160px;
        }
        .page-break {
            page-break-before: always;
        }
        @page {
            margin: 140px 25px 100px 25px;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('assets/images/nduvini_header.png') }}" alt="Header Image" style="height: 130px; width: 100%;">
    <hr>
    <table style="border: none;">
        <tr>
            <td width="70%">
                @if (count($purchases))
                    <p>{{ $purchases[0]->supplier_name ?? '' }}</p>
                    <p>{{ $purchases[0]->address ?? '' }}</p>
                    <p>{{ $purchases[0]->place ?? '' }}</p>
                @endif
            </td>
            <td>
                <p><strong>STATEMENT</strong></p>
                <p>Period: 
                    @if ($startdate == '' && $enddate == '') 
                        ALL
                    @else
                        {{ $startdate }} TO {{ $enddate }}
                    @endif
                </p>
                <p>Page: <span class="page-number"></span></p>
            </td>
        </tr>
    </table>
</header>

<main>
    <table id="sales_report">
        <thead>
            <tr>
                <th>Date</th>
                <th>Doc. No</th>
                <th>Reference</th>
                <th>Invoice Amount</th>
                @if ($payment == '1')
                    <th>Credit</th>
                @elseif ($payment == '')
                    <th>Debit</th>
                    <th>Credit</th>
                @else
                    <th>Debit</th>
                @endif
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchases as $s)
                <tr>
                    <td>{{ $s->created_date }}</td>
                    <td>PI {{ $s->invoice_number }}</td>
                    <td>{{ $s->lpo_number }}</td>
                    <td>{{ number_format($s->total_amount, 2) }}</td>

                    @if ($payment == '1')
                        <td>{{ number_format($s->paid_amount, 2) }}</td>
                        <td>{{ number_format($s->total_amount - $s->paid_amount, 2) }}</td>
                    @elseif ($payment == '')
                        <td>{{ number_format(max(0, $s->total_amount - $s->paid_amount), 2) }}</td>
                        <td>{{ number_format($s->paid_amount, 2) }}</td>
                        <td>{{ number_format($s->total_amount - $s->paid_amount, 2) }}</td>
                    @else
                        <td>{{ number_format(max(0, $s->total_amount - $s->paid_amount), 2) }}</td>
                        <td>{{ number_format($s->total_amount - $s->paid_amount, 2) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding-top: 20px;">No Data Found</td>
                </tr>
            @endforelse

            
        </tbody>
    </table>
</main>

<footer>
    
</footer>

</body>
</html>
