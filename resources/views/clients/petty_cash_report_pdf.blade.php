<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Petty Cash Report') }}</title>

   <style>
       h4 {
          text-align: center;
      }
      tr th {
           background: #eee;
           padding-top: 10px;
           font-size: 13px;
      }
      h5 {
          text-align: center;
          font-size: 0.8em;
      }
      table {
          font-size: 13px;
      }
      #sales_report {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #total_estimate {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 35%;
        }
        #total_estimate td, #sales_report th {
        border: 1px solid #ddd;
        /* padding: 3px 0 3px 3px; */
        }

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 4px 10px 3px 10px;
        font-size: 0.9em;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        .page-break, .page-break .part-header  {
            /* page-break-before: always; */
            margin-top: 150px;
        }
        .part-header  {
            /* page-break-before: always; */
            position: absolute;
            top: 150px; /* Adjust the distance as needed */
            left: 0;
            width: 100%;
        }

        .page-break-after  {
            /* page-break-before: always; */
            margin-top: 250px;
        }

        
       #sales_report tr:nth-child(6n+1) {
        /* page-break-after: always; */
        }
       
        #sales_report th {
        padding-top: 6px;
        padding-bottom: 6px;
        text-align: left;
        }
        .footer {
          position: fixed;
          bottom: 0;
          width: 100%;
        }
        .footer .left{
           /* font-size: 1em; */
        }
        .footer .footer_ {
          float: right;
        }
        #line {
            border: 1px solid #ddd;
        }
        #rpt h4 {
            text-align: right;
        }
        .header {
            position: fixed;
            top: -46px;
            width: 100%;
            height: 45px;
            font-size: 20px !important;
            /* background-color: #000; */
            /* color: white; */
            /* text-align: center; */
            /* line-height: 35px; */
        }
          div .logo {
          /* /* display: block; */
          margin-left: 0;
          margin-top: 0;
          /* width: 50%;  */
          text-align: left;
          /* padding: 0 0 20px 0; */
       }
        #rpt h4 {
            text-align: right;
        }
        #line {
            border: 1px solid #ddd;
        }
   </style>

</head>
<body>
    <header>
        <div class="header">
         <div class="logo">
         <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 730px;" class="center img-thumbnail img-responsive profile-pic">    
      </div>
 </header>
<br/><br/><br/><br/><br/>
      <div id="line"></div>
    <h4> PETTY CASH REPORT </h4>
    <h5> 
    @if(!$petty)
       ( ALL )
    @else 
     {{ $petty->name }}
    @endif
    </h5>
    <h5> FROM : {{ $startdate  }} <strong> TO : </strong>  {{ $enddate }} </h5>
        <div id="line"></div>
       <br/>     
    <div class="container">
           <div class="row">
                 <div class="col-md-12">
                    <div>
                     <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Expense Name</th>
                                <th>Paid To </th>
                                <th>Voucher No. </th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($petty_cash as $key=>$p)
                                <tr>
                                <td style="text-align:center;">{{ $key+1 }} </td>
                                <td> {{ strtoupper($p->note) }}</td>
                                <td> {{ $p->paid_to }} </td>
                                <td> {{ $p->voucher_no }} </td>
                                <td> {{ number_format($p->amount, 2) }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tr>
                                <td> </td>
                                <td colspan="3" style="text-align: right;"> Total Amount : &nbsp; &nbsp; &nbsp; </td>
                                <td><strong> {{ number_format($total_amounts[0]->total_amount, 2) }} </strong></td>
                            </tr>

                     </table>  
                    </div>
                </div> 

    </div>
</div>
</body>
</html>
