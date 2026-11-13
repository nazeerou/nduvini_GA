<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Delivery Note') }}</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 350px 45px 50px 45px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
        }

        table {
            font-size: 12px;
            border-collapse: collapse;
        }

        tr {
            page-break-inside: avoid;
        }

        #sales_report {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
            page-break-inside: auto;
        }

        #total_estimate {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 35%;
        }

        #total_estimate td,
        #sales_report th {
            border: 1px solid #ddd;
            padding: 8px 0 8px 8px;
        }

        #sales_report td,
        #sales_report th {
            border: 1px solid #ddd;
            padding: 4px 0 3px 15px;
            font-size: 0.9em;
        }

        #sales_report tr:hover {
            background-color: #ddd;
        }

        #sales_report th {
            padding-top: 6px;
            padding-bottom: 6px;
            text-align: left;
            background: #eee;
            font-size: 13px;
        }

        /* Pagination */
        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        #sales_report thead {
            display: table-header-group;
        }

        #sales_report tbody tr {
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
            margin-top: 155px;
        }

        /* Header */
        header {
            position: fixed;
            top: -335px;
            width: 100%;
            font-size: 20px !important;
        }

        #line {
            border: 1px solid #ddd;
            margin-top: 3px;
        }

        #line1 {
            border-bottom: 1px solid #ddd;
        }

        .branch {
            font-size: 0.8em;
            padding: 3px 0 2px 26px;
        }

        .business_name {
            text-align: left;
            padding-left: 14px;
            font-size: 22px;
            font-weight: 300;
        }

        .logo {
            margin-left: 0;
            margin-top: 0;
            text-align: left;
        }

        h5 {
            font-size: 1em;
        }

        h4,
        h2 {
            color: blue;
        }

        p {
            line-height: 1.3;
            margin: 5px 0;
        }

        /* Footer */
        footer {
    position: fixed;
    bottom: -150px;
    left: 0;
    right: 0;
    height: 200px;
    font-size: 20px !important;
    width: 100%;
}

/* Prevent content from entering footer area */
main {
    width: 100%;
    padding-bottom: 15px;
}

/* Keep table rows together */
#sales_report {
    page-break-inside: auto;
}

#sales_report thead {
    display: table-header-group;
}

#sales_report tbody tr {
    page-break-inside: avoid;
}

/* Prevent signature section from splitting */
#tbl_sign {
    page-break-inside: avoid;
    margin-top: 25px;
}

/* Keep content away from page bottom */
.container {
    padding-bottom: 15px;
}

/* Avoid unnecessary blank space */
.page-break {
    page-break-before: always;
    margin-top: 0;
}

        footer #footer_ {
            background-color: red;
            color: #fff;
        }

        footer table {
            width: 700px;
            border: 0;
            padding: 0;
            margin: 0;
            background-color: red;
            color: #fff;
        }

        footer td {
            vertical-align: top;
        }

        ul {
            list-style-type: none;
            margin: 0;
            padding: 10px;
        }

        li {
            margin-bottom: 2px;
        }

        .footer .left {
            /* font-size: 1em; */
        }

        .footer .footer_ {
            float: right;
        }

        /* ================= WATERMARK =================
           position: fixed  → repeats on every page
           explicit top/left/width/height → DomPDF ignores transform
           hardcoded opacity → DomPDF ignores CSS variables
           z-index: -1 → paints behind body content
        ============================================ */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('assets/images/logo_nduvini.jpeg');
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 400px;
            opacity: 0.08;
            z-index: -1;
            pointer-events: none;
        }

        /* Content */
        .subject {
            font-size: 1em;
            padding: 6px 0;
        }

        .content {
            font-size: 0.9em;
            line-height: 1.5;
        }

        .accept {
            padding: 4px;
            font-size: 1.1em;
        }

        /* Signature */
        #tbl_sign {
            width: 100%;
            border: 0;
            padding: 0;
            margin: 0;
        }

        #tbl_sign tr {
            page-break-inside: avoid;
        }

        #tbl_sign tr td {
            padding-top: 10px;
            vertical-align: top;
        }

    </style>
</head>

<body>

@php
    $sale = $sales[0];

    $deliveryNumber = $sale->job_card_ID ?? $sale->invoice_number;

    $deliveryNumber = str_pad(
        (string) $deliveryNumber,
        6,
        '0',
        STR_PAD_LEFT
    );

    $deliveryDate = !empty($sale->delivery_date)
        ? \Carbon\Carbon::parse($sale->delivery_date)->format('Y-m-d')
        : $sale->created_date;

    $deliveryDateLong = !empty($sale->delivery_date)
        ? \Carbon\Carbon::parse($sale->delivery_date)->format('d M, Y')
        : (!empty($sale->created_date)
            ? \Carbon\Carbon::parse($sale->created_date)->format('d M, Y')
            : '');
@endphp

{{-- ================= HEADER ================= --}}

<header>

    <img
        src="{{ public_path().'/assets/images/nduvini_header.png' }}"
        alt="Nduvini Auto Works"
        style="height: 130px; width: 700px;"
        class="center img-thumbnail img-responsive profile-pic"
    >

    <div id="line1"></div>

    <div class="branch">

        @if(Auth::user()->branch_id == 1)
            HEAD OFFICE : Dar es Salaam

        @elseif(Auth::user()->branch_id == 2)
            BRANCH : TABORA

        @elseif(Auth::user()->branch_id == 3)
            BRANCH : DODOMA
        @endif

    </div>

    <div id="line"></div>

    <table style="border: 0; width: 700px;">

        <tr>

            <td width="500px">

                <h4>Deliver To:</h4>

                <p>{{ $client_name[0]->client_name ?? $sale->customer_name }}</p>

                <p>{{ $client_name[0]->address ?? '' }}</p>

                <p>{{ $client_name[0]->place ?? '' }}</p>

            </td>

            <td>

                <h4>DELIVERY NOTE</h4>

                <p>
                    Delivery # :
                    <span style="float:right">
                        {{ $deliveryNumber }}
                    </span>
                </p>

                <p>
                    Delivery Date :
                    <span style="float:right">
                        {{ $deliveryDate }}
                    </span>
                </p>

                <p>
                    P.O # :
                    <span style="float:right">
                        {{ $sale->reference }}
                    </span>
                </p>

            </td>

        </tr>

    </table>

</header>

{{-- ================= FOOTER ================= --}}

<footer>

    <table>

        <tr>

            <td>

                <ul>
                    <li>HEAD OFFICE : Temeke District - Chang'ombe</li>
                    <li>P.o Box 45610 DSM</li>
                    <li>Tel: +255 22 2860279 | Fax +255 22 2860276</li>
                    <li>Phone: +255 754 275223</li>
                    <li>Email: nduvini@hotmail.com</li>
                </ul>

            </td>

            <td>

                <ul>
                    <li>Branch Office : Singida Road- Kizota</li>
                    <li>P.o Box 530 DODOMA</li>
                    <li>Tel: +255 26 2340153</li>
                    <li>Phone: +255 753 915234</li>
                    <li>Email: dodoma@nduviniautoworks.co.tz</li>
                </ul>

            </td>

            <td>

                <ul>
                    <li>Branch Office : Tabora</li>
                    <li>P.o Box 1057 Tabora</li>
                    <li>Tel: +255 22 2860279</li>
                    <li>Phone: +255 754 275223</li>
                    <li>Email: nduvini@hotmail.com</li>
                </ul>

            </td>

        </tr>

    </table>

</footer>

{{-- ================= MAIN CONTENT ================= --}}

<main>

    <div class="watermark"></div>

    {{-- Vehicle Information --}}

    <table id="sales_report" class="table table-striped table-bordered">

        <thead>
            <tr>
                <th>Vehicle Reg.</th>
                <th>Make</th>
                <th>Model</th>
                <th>Chassis No.</th>
                <th>Mileage</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <td>{{ $sale->vehicle_reg }}</td>
                <td>{{ $sale->emake }}</td>
                <td>{{ $sale->emodel }}</td>
                <td>{{ $sale->chassis }}</td>
                <td>{{ $sale->milleage }}</td>
            </tr>

        </tbody>

    </table>

    <div class="container table-responsive">

        <br>

        <div class="subject">
            <strong>RE: COMPLETION OF VEHICLE REPAIRS</strong>
        </div>

        <span class="content">

            I would like to inform you that the repairs of the aforementioned
            vehicle, which was taken to the workshop

            <strong>NDUVINI AUTO WORKS LTD</strong>

            on {{ $deliveryDateLong }} have been completed.

        </span>

        <h4>
            <i>
                <strong>The repairs carried out include the following:</strong>
            </i>
        </h4>

        {{-- Repaired Parts --}}

        <table id="sales_report" class="table table-striped table-bordered">

            <thead class="part-header">

                <tr>
                    <th>#</th>
                    <th>Particular</th>
                    <th>Qty</th>
                </tr>

            </thead>

            <tbody id="tableBody">

                @forelse($sales as $key => $product)

                    <tr>

                        <td>{{ $key + 1 }}</td>

                        <td>
                            {{ $product->item_name }}
                        </td>

                        <td>
                            {{ $product->qty }}

                            @if($product->qty > 1)
                                {{ strtoupper($product->purchase_unit) }}S
                            @else
                                {{ strtoupper($product->purchase_unit) }}
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="3">
                            No repaired parts recorded.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <br>

    </div>

    {{-- ================= SIGNATURE SECTION ================= --}}

    <br>

    <table id="tbl_sign">

        <tr>

            <td>
                Office Representative :
                .........................................................................
            </td>

            <td>
                Position : ...........................
            </td>

            <td>
                Mobile : ......................
            </td>

        </tr>

        <tr>

            <td colspan="3">

                <div class="accept">
                    <i>
                        I am satisfied with the repairs of this vehicle
                        as listed above.
                    </i>
                </div>

            </td>

        </tr>

        <tr>

            <td>
                Signature: ...........................................
            </td>

            <td>
                Date : .................................
            </td>

        </tr>

        <tr>

            <td>
                Workshop Representative :

                <strong>
                    {{ strtoupper(Auth::user()->fname) }}
                    {{ strtoupper(Auth::user()->lname) }}
                </strong>
            </td>

            <td>
                Position :

                <strong>
                    {{ $user_role->name ?? '' }}
                </strong>
            </td>

            <td>
                Signature : .............................
            </td>

        </tr>

    </table>

</main>

</body>
</html>