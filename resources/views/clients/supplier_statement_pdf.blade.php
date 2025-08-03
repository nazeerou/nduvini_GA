<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Supplier Statement') }}</title>
    <style>
     
     h4 {
          /* text-align: center; */
      }
      tr th {
           background: #eee;
           padding-top: 10px;
           font-size: 13px;
      }
      h5 {
          text-align: right;
          font-size: 1em;
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
        width: 50%;
        }

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 8px 0 6px 8px;
        font-size: 0.9em;

        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding: 8px;
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
          div .logo {
          /* /* display: block; */
          margin-left: 0;
          margin-top: 0;
          /* width: 50%;  */
          text-align: left;
          /* padding: 0 0 20px 0; */
       }
     .business_name {
          text-align: left;
          padding-left: 14px;
          font-size: 22px;
          font-weight: 300;
     }
ul {
  list-style-type: none;
  margin: 0;
  padding: 10;
}

main {
        /* background-image: url("assets/images/logo_nduvini.jpeg"); */
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        opacity: 0.1;
        background-size: 500px 500px;

    }

    @page {
        margin: 140px 25px 100px 25px;
        /* padding: 200px 0; */
        /* padding-bottom: 20px; */
       }
       
       .page-break  {
            page-break-before: always;
            margin-top: 620px;
        }

        header {
            position: fixed;
            top: -136px;
            width: 100%;
            height: 55px;
            font-size: 20px !important;
            /* background-color: #000; */
            /* color: white; */
            /* text-align: center; */
            /* line-height: 35px; */
        }
        footer {
            position: fixed; 
            bottom: -95px; 
            height: 90px; 
            font-size: 20px !important;
            width: 100%;
            text-align: right;
            /* line-height: 35px; */
        }
        footer .page-number {
            /* text-align: center; */
        }
        .page-number:before {
            content:  counter(page);
        }
        #footer_details {
            /* display: inline; */
        }
        #footer_details .account_details {
            float: left;
            width: 280px;
            font-size: 8px;
        }
        #footer_details .total_summations {
            float: right;
            width: 500px;
            text-align: right;
        }
   </style>
</head>
<body>
 <header>
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 760px;" class="center img-thumbnail img-responsive profile-pic">    

 <div id="line"></div>
<table style="border: 0px" width="745px">
<tr>
    <td width="510px">
    @if (count($purchases) != null)
       <p> {{ $purchases[0]->supplier_name ?? null }} </p>
       <p> {{ $purchases[0]->address ?? null }}</p>
       <p>{{ $purchases[0]->place ?? null }}</p>
       <p></p>
       <p></p>
       @else 
       @endif
    </td>
    <td>
    <p><strong>STATEMENT</strong> </p>
    <p>Period : 

      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      @if ($startdate == '' && $enddate == '') 
          {{ 'ALL ' }}
        @else
         {{ $startdate }} TO  {{ $enddate }} 
      @endif
    </p> 
    <p>Page :   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
    <span class="page-number"></span> 
    </p>     
    <!-- <p>Page : </p>      -->
    </td>
    
</tr>
</table>
</header>
<br/><br/><br/></br/><br/><br/><br/>
 <!-- <span id="rpt"><h4> </h4></span> -->
     <br/>
<footer>
<!-- <div class="footer" style="background: #ff0000; color: #fff"> -->
<!-- <div id="line"></div> -->
<table style="border: 1px solid #ddd;" class="table table-bordered" width="250px" align="right">
   <tr>
    <td align="left">
       &nbsp; OutStanding Balance : 
      </td>
      <td> {{ number_format(($total_charges[0]->total_amount - $total_credit[0]->paid_amount), 2) }} </td>
   </tr>
</table>
<!-- </div> -->
</div>
</footer>
 <div id="line"></div>
 <br/><br/>
<span id="rpt"></div>
<main>
             <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc; background: #ddd;">
                                <td>Date </td>
                                <td>Doc. No </td>
                                <td>Reference </td>
                                <td>Invoice Amount </td>
                                @if ($payment == '1')
                                <td>Credit </td>
                                @elseif ($payment == '')
                                <td>Debit </td>
                                <td>Credit </td>
                                @else
                                <td>Debit </td>
                                @endif
                                <td>Balance </td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($purchases) > 0)
                            @foreach($purchases as $s)
                            <tr  style="border-bottom: 1px solid #D9FAF9">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                PI {{ $s->invoice_number }}
                                </td>
                                <td> {{ $s->lpo_number }}  </td>
                                <td> {{ number_format($s->total_amount, 2) }} </td>
                                @if ($payment == '1')
                                <td> {{ number_format($s->paid_amount, 2) }} </td>
                                <td>
                                    {{ number_format(($s->total_amount -$s->paid_amount), 2) }}
                                 </td>
                                @elseif ($payment == '')
                                <td> 
                                    @if ($s->paid_amount >= $s->total_amount)
                                    {{ '0.00' }}
                                    @else 
                                    {{ number_format(($s->total_amount -$s->paid_amount), 2) }}
                                    @endif
                                 </td>
                                <td> {{ number_format($s->paid_amount, 2) }} </td>
                                <td> {{ number_format(($s->total_amount -$s->paid_amount), 2) }} </td>
                                 @else 
                                 <td>
                                 @if ($s->paid_amount >= $s->total_amount)
                                    {{ '0.00' }}
                                    @else 
                                    {{ number_format(($s->total_amount -$s->paid_amount), 2) }}
                                    @endif   
                                 </td>
                                 <td> {{ number_format(($s->total_amount -$s->paid_amount), 2) }} </td>
                                 @endif
                            </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="7"> No Data Found </td>
                               </tr>
                            @endif
                        </tbody>
              <!-- <tfooter> -->
             <tr style="background: #FAF6F6;">
                 <td colspan="3" style="text-align: right"> Total Amount &nbsp;&nbsp; : &nbsp;&nbsp; </td>
                 <td> {{ number_format($total_charges[0]->total_amount, 2) }}</td>
                 @if ($payment == '1')
                 <td>{{ number_format($total_credit[0]->paid_amount, 2) }}</td>
                 <td>
                    {{ number_format(($total_charges[0]->total_amount-$total_credit[0]->paid_amount), 2) }}
                </td>

                 @elseif ($payment == '')
                 <td>
                  @if ($total_credit[0]->paid_amount >= $total_charges[0]->total_amount)
                       {{ '0.00' }}
                    @else
                    {{ number_format(($total_charges[0]->total_amount-$total_credit[0]->paid_amount), 2) }}
                    @endif
                 </td>
                 <td>{{ number_format($total_credit[0]->paid_amount, 2) }}</td>
                 <td>{{ number_format(($total_charges[0]->total_amount-$total_credit[0]->paid_amount), 2) }}</td>
                 @else 
                 <td>
                 @if ($total_credit[0]->paid_amount >= $total_charges[0]->total_amount)
                       {{ '0.00' }}
                    @else
                    {{ number_format(($total_charges[0]->total_amount-$total_credit[0]->paid_amount), 2) }}
                    @endif
                 </td>
                 <td>{{ number_format(($total_charges[0]->total_amount-$total_credit[0]->paid_amount), 2) }}</td>
                 @endif
            </tr>
           <!-- </tfooter> -->
            </table>
       <br/><br/>
      </main>
        </div> 
    <div>
</div>

</body>
</html>