<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Profoma Invoice') }}</title>

    <style>
    
        @page {
            margin: 285px 40px 125px 40px; /* [HDR] */
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'InvoiceSans', 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #222;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* ================= HEADER ================= */

        header {
            position: fixed;
            top: -280px;   /* [HDR] */
            left: 0;
            right: 0;
            height: 230px; /* [HDR] */
            width: 100%;
        }

        .invoice-logo {
            width: 100%;
            height: 125px;
            display: block;
        }

        .branch {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 0;
        }

        .header-line {
            border-top: 1px solid #aaa;
            margin: 3px 0 5px;
        }

        .invoice-info {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-info td {
            border: none;
            vertical-align: top;
            padding: 2px 5px;
            font-size: 10px;
            line-height: 1.45;
        }

        .invoice-info p {
            margin: 2px 0;
            line-height: 13px;
        }

        .invoice-info strong {
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        .invoice-title {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-align: right;
        }

        /* Tabular figures keep amounts vertically aligned in columns */
        .invoice-number,
        .price_cell,
        #total_estimate td,
        .grand-total td {
            font-variant-numeric: tabular-nums;
            -moz-font-feature-settings: "tnum";
            font-feature-settings: "tnum";
        }

        .page-counter:before {
            content: counter(page);
        }

        /* ================= FOOTER ================= */

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 95px;
            width: 100%;
            background-color: #f8312a;
            color: #ffffff;
        }

        .footer-table {
            width: 100%;
            padding: 10px 8px 5px 8px;
            border-collapse: collapse;
            background-color: #f8312a;
        }

        .footer-table td {
            border: none;
            width: 33.33%;
            vertical-align: top;
            padding: 2px 7px;

            font-size: 8px;
            line-height: 13px;
            color: #ffffff;
        }

        .footer-table strong {
            color: #ffffff;
            font-size: 9px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer-table ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer-table li {
            color: #ffffff;
        }

        .page-number {
            text-align: right;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.4px;
            color: #ffffff;
            padding-top: 3px;
            border-top: 1px solid rgba(255, 255, 255, 0.4);
        }

        /* ================= GENERAL TABLES ================= */

        table {
            border-collapse: collapse;
            width: 100%;
        }

        #sales_report {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        #sales_report thead {
            display: table-header-group;
        }

        #sales_report tfoot {
            display: table-footer-group;
        }

        #sales_report th {
            background: #eee;
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: left;

            font-size: 9.5px;
            font-weight: bold;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        #sales_report td {
            border: 1px solid #ddd;
            padding: 4px;

            font-size: 10px;
            line-height: 1.4;
            vertical-align: top;
            word-wrap: break-word;
        }

        #sales_report tbody tr {
            page-break-inside: avoid;
        }

        #sales_report {
            page-break-inside: auto;
        }

        .vehicle-table td,
        .vehicle-table th {
            padding: 4px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin: 8px 0 5px;
        }

        /* ================= TOTALS ================= */

        .summary-wrapper {
            width: 100%;
            page-break-inside: avoid;
        }

        .summary-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }

        .total-summations {
            width: 48%;
            float: right;
        }

        #total_estimate {
            width: 100%;
            border-collapse: collapse;
        }

        #total_estimate td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 10px;
            line-height: 1.4;
        }

        #total_estimate td:last-child {
            text-align: right;
            width: 120px;
            font-weight: bold;
        }

        .grand-total td {
            background: #eee;
            font-weight: bold;
            font-size: 12px !important;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .clear {
            clear: both;
        }

        /* ================= WATERMARK ================= */

        .watermark {
            position: fixed;
            top: 15%;
            left: 10%;
            width: 80%;
            height: 350px;
            background-image: url('{{ public_path("assets/images/logo_nduvini.jpeg") }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.07;
            z-index: -1;
        }

        /* ================= PRINT/PAGINATION ================= */

        .no-break {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        table {
            page-break-inside: auto;
        }

        .price_cell {
            padding-right: 10px !important;
            text-align: right !important;
        }
    </style>
</head>

<body>

@php
    $invoice = $sales[0];

    $client = isset($client_name[0]) ? $client_name[0] : null;

    $profomaNumber = str_pad($invoice->profoma_invoice, 6, '0', STR_PAD_LEFT);

    $accountNumber = $invoice->account_prefix .
        str_pad($invoice->account_no, 3, '0', STR_PAD_LEFT);

    $partsTotal = (float) $total_sales;
    $labourTotal = (float) $total_labours;
    $temesaTotal = (float) ($temesa_fee ?? 0);
@endphp


<!-- =====================================================
     REPEATED PROFOMA HEADER
===================================================== -->

<header>

    <img
        src="{{ public_path('assets/images/nduvini_header.png') }}"
        class="invoice-logo"
        alt="Company Header"
    >

    <div class="branch">
        @if(Auth::user()->branch_id == 1)
            HEAD OFFICE : DAR ES SALAAM
        @elseif(Auth::user()->branch_id == 2)
            BRANCH : TABORA
        @elseif(Auth::user()->branch_id == 3)
            BRANCH : DODOMA
        @endif
    </div>

    <div class="header-line"></div>

    <table class="invoice-info">
        <tr>

            <!-- CUSTOMER DETAILS -->

            <td width="68%">

                @if($client && !empty($client->client_name))

                    <p><strong>{{ $client->client_name }}</strong></p>
                    <p>{{ $client->address }}</p>
                    <p>{{ $client->place }}</p>

                    <p>
                        TIN: {{ $client->tin }}
                        &nbsp; VRN: {{ $client->vrn }}
                    </p>

                @else

                    <p><strong>{{ $invoice->customer_name }}</strong></p>

                @endif

            </td>

            <!-- PROFOMA DETAILS -->

            <td width="30%">

                <p class="invoice-title">PROFOMA INVOICE</p>
                <p>Estimate Date:</p>
                <p>Account No:</p>
                <p>Order Ref:</p>
                <p>Estimate Valid To:</p>

            </td>

            <!-- PROFOMA VALUES -->

            <td width="20%" style="text-align:right;">

                <p class="invoice-number">
                    {{ $profomaNumber }}
                </p>

                <p>{{ $invoice->created_date }}</p>

                <p>{{ $accountNumber }}</p>

                <p>{{ $invoice->reference }}</p>

                <p>{{ $invoice->valid_estimate_date }}</p>

            </td>

        </tr>
    </table>

</header>


<!-- =====================================================
     REPEATED FOOTER
===================================================== -->

<footer>

    <table class="footer-table">
        <tr>

            <td>
                <strong>HEAD OFFICE - DAR ES SALAAM</strong>
                <ul>
                    <li>Temeke District - Chang'ombe</li>
                    <li>P.O Box 45610 DSM</li>
                    <li>Tel: +255 739275223</li>
                    <li>Fax: +255 22 2860276</li>
                    <li>Phone: +255 754 275223</li>
                    <li>Email: nduvin@hotmail.com</li>
                </ul>
            </td>

            <td>
                <strong>DODOMA BRANCH</strong>
                <ul>
                    <li>Singida Road - Kizota</li>
                    <li>P.O Box 530 DODOMA</li>
                    <li>Tel: +255 738692607</li>
                    <li>Phone: +255 753 915234</li>
                    <li>Email: dodoma@nduviniautoworks.co.tz</li>
                </ul>
            </td>

            <td>
                <strong>TABORA BRANCH</strong>
                <ul>
                    <li>Tabora</li>
                    <li>P.O Box 45610 Tabora</li>
                    <li>Tel: +255 738310148</li>
                    <li>Phone: +255 754 275223</li>
                    <li>Email: tabora@nduviniautoworks.co.tz</li>
                </ul>
            </td>

        </tr>
    </table>

    <div class="page-number">
        Page | <span class="page-counter"></span>
    </div>

</footer>


<!-- =====================================================
     DOCUMENT BODY
===================================================== -->

<div class="watermark"></div>


<!-- VEHICLE INFORMATION -->

<div class="section-title"></div>

<table id="sales_report" class="vehicle-table">

    <thead>
        <tr>
            <th width="20%">Vehicle Reg</th>
            <th width="20%">Make</th>
            <th width="20%">Model</th>
            <th width="25%">Chassis No.</th>
            <th width="15%">Mileage</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>{{ $invoice->vehicle_reg }}</td>
            <td>{{ $invoice->emake }}</td>
            <td>{{ $invoice->emodel }}</td>
            <td>{{ $invoice->chassis }}</td>
            <td>{{ $invoice->milleage }}</td>
        </tr>
    </tbody>

</table>


<!-- =====================================================
     PARTS TABLE - AUTOMATIC PAGE BREAKS
===================================================== -->

<div class="section-title"></div>

<table id="sales_report">

    <thead>
        <tr>
            <th width="5%">SN</th>
            <th width="43%">Parts</th>
            <th width="12%">Qty</th>
            <th width="15%">Unit</th>
            <th width="10%">D</th>
            <th width="20%">Sub Total</th>
        </tr>
    </thead>

    <tbody>

        @foreach($sales as $key => $product)

            <tr>
                <td>{{ $key + 1 }}</td>

                <td>
                    {{ strtoupper($product->item_name) }}
                </td>

                <td>
                    {{ $product->qty }}

                    @if($product->qty > 1)
                        {{ strtoupper($product->purchase_unit) }}S
                    @else
                        {{ strtoupper($product->purchase_unit) }}
                    @endif
                </td>

                <td class="price_cell">
                    {{ number_format($product->selling_price, 2) }}
                </td>

                <td class="price_cell">
                    {{ $product->discount ? $product->discount.'%' : '' }}
                </td>

                <td class="price_cell">
                    {{ number_format($product->total_sales, 2) }}
                </td>

            </tr>

        @endforeach

    </tbody>

</table>


<!-- =====================================================
     LABOUR TABLE - AUTOMATIC PAGE BREAKS
===================================================== -->

@if(count($labours) > 0)

    <div class="section-title"></div>

    <table id="sales_report">

        <thead>
            <tr>
                <th width="43%">Labour</th>
                <th width="12%">Rate/Hr</th>
                <th width="15%">Unit</th>
                <th width="10%">D</th>
                <th width="20%">Sub Total</th>
            </tr>
        </thead>

        <tbody>

            @foreach($labours as $key => $l)

                <tr>

                    <td>
                        {{ strtoupper($l->labour_name) }}
                    </td>

                    <td>
                        {{ $l->qty }}
                        @if($l->qty > 1 && $l->rate !== "%")
                            {{ $l->rate ?? '' }}
                        @else
                            {{ $l->rate ?? '' }}
                        @endif
                    </td>

                    <td class="price_cell">
                        {{ number_format($l->charge) }}
                    </td>

                    <td></td>

                    <td class="price_cell">
                        {{ number_format($l->total_amount, 2) }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

@endif


<!-- =====================================================
     FINAL SUMMARY - DISPLAY ONLY ONCE
===================================================== -->
<br/>
<div class="summary-wrapper no-break">
<br/><br/>
    <div class="total-summations">

        <table id="total_estimate">

            @if($total_discounts != null && $total_discounts > 0)

                <tr>
                    <td><strong>Net Discount:</strong></td>
                    <td>{{ number_format($total_discounts, 2) }}</td>
                </tr>

            @endif

            <tr>
                <td>Parts:</td>
                <td>{{ number_format($partsTotal, 2) }}</td>
            </tr>

            <tr>
                <td>Labour:</td>
                <td>{{ number_format($labourTotal, 2) }}</td>
            </tr>

            @if($temesaTotal > 0)

                <tr>
                    <td>TEMESA Fee (8%):</td>
                    <td>{{ number_format($temesaTotal, 2) }}</td>
                </tr>

            @endif

            <tr>
                <td><strong>Sub Total:</strong></td>
                <td>
                    {{ number_format($partsTotal + $labourTotal + $temesaTotal, 2) }}
                </td>
            </tr>

            <tr>
                <td>VAT Amount (18%):</td>
                <td>{{ number_format($vat_charges, 2) }}</td>
            </tr>

            <tr class="grand-total">
                <td>TOTAL ESTIMATE:</td>
                <td>{{ number_format($grand_total_amount, 2) }}</td>
            </tr>

        </table>

    </div>

    <div class="clear"></div>

</div>


</body>
</html>