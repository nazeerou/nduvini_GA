<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Client Statement') }}</title>
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

main1 {
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
        margin: 50px 25px 100px 25px;
        /* padding: 200px 0; */
        /* padding-bottom: 20px; */
       }
       
       .page-break  {
            page-break-before: always;
            margin-top: 620px;
        }

        header {
            position: relative;
            top: -50px;
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
        p {
            line-height: 0.8;
        }
   </style>
</head>
<body>
 <header>
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 760px;" class="center img-thumbnail img-responsive profile-pic">    

 <div id="line"></div>
<table style="border: 0px" width="745px">
<tr>
    <td width="500px">

       <p> {{ $clients[0]->client_name ?: '' }} </p>
       <p> {{ $clients[0]->address ? : ''}} </p>
       <p> {{ $clients[0]->place ? : ''}} </p>
       <p></p>
       <p> TIN :  {{ $clients[0]->tin }}  &nbsp;&nbsp;  VRN :  {{ $clients[0]->vrn }}  </p>
       <p></p>
       <p></p>
       <p></p>
    </td>
    <td>
    <p><strong>STATEMENT</strong> </p>
    <p>Period : 
     &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    @if ($startdate == '' && $enddate == '') 
          {{ 'ALL ' }}
        @else
         {{ $startdate }} TO  {{ $enddate }} 
      @endif
    </p> 
    <p>Account No :
    &nbsp;&nbsp;&nbsp;&nbsp;
    @if (count($sales) != 0) 
         {{ $sales[0]->account ?? null }}
        @else 
          NULL
        @endif
  </p> 
    <p>Page :
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    &nbsp;&nbsp;&nbsp;
    <span class="page-number"></span> 
    </p>
</tr>
</table>
</header>
</br/><br/><br/><br/><br/><br/><br/><br/><br/>
 <!-- <span id="rpt"><h4> </h4></span> -->
   
<footer>
<!-- <div class="footer" style="background: #ff0000; color: #fff"> -->
<!-- <div id="line"></div> -->
<table style="border: 1px solid #ddd;" class="table table-bordered" width="250px" align="right">
   <tr>
    <td align="left">
    @if ($payment == 1)
    &nbsp; Total Credit :  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{ number_format($total_credit, 2) }}
    @elseif ($payment == 2)
    &nbsp; Total  Debit :
    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    {{ number_format(($total_charges - $total_credit), 2) }}
    @else
       &nbsp; OutStanding Balance : 
      </td>
      <td>{{ number_format(($total_charges - $total_credit), 2) }} </td>
    @endif
   </tr>
</table>
<!-- </div> -->
</div>
</footer>
 <div id="line"></div>
<br/>
<span id="rpt"></div>
<main>

         @if($payment == 1)
             <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc; background: #ddd;">
                                <td>Date </td>
                                <td>Doc. No </td>
                                <td>Description</td>
                               
                                <td>Charges </td>
                                <td>Credit </td>
                            </tr>
                        </thead>
                        <tbody>
                        
                        @if(count($sales) > 0)
                            @foreach($sales as $s)
                            <tr  style="border-bottom: 1px solid #D9FAF9">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                @if ($s->invoice_number < 10)
                                SI {{ "00000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100)
                                SI {{ "0000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 1000)
                                SI {{ "000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 10000)
                                SI {{ "00".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100000)
                                SI {{ "0".$s->invoice_number }}
                                @else 
                                SI {{ $s->invoice_number }}
                                @endif
                                </td>
                                <td> {{ $s->vehicle_reg }} </td>
                              
                                <td> {{ number_format($s->bill_amount, 2) }} </td>
                                <td> {{ number_format($s->paid_amount, 2) }} </td>
                              </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="8"> No Data Found </td>
                               </tr>
                            @endif
                        </tbody>
              <!-- <tfooter> -->
             <tr style="background: #FAF6F6;">
                 <td colspan="3" style="text-align: right"> Total Amount &nbsp;&nbsp; : &nbsp;&nbsp; </td>
                 <td> {{ number_format($total_charges, 2) }}</td>
                 <td>{{ number_format($total_credit, 2) }}</td>
            </tr>
           <!-- </tfooter> -->
            </table>
       <br/><br/>
       @elseif ($payment == 2)
       <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc; background: #ddd;">
                                <td>Date </td>
                                <td>Doc. No </td>
                                <td>Description</td>
                               
                                <td>Charges </td>
                                <td>Debit </td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($sales) > 0)
                            @foreach($sales as $s)
                            <tr  style="border-bottom: 1px solid #D9FAF9">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                @if ($s->invoice_number < 10)
                                SI {{ "00000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100)
                                SI {{ "0000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 1000)
                                SI {{ "000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 10000)
                                SI {{ "00".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100000)
                                SI {{ "0".$s->invoice_number }}
                                @else 
                                SI {{ $s->invoice_number }}
                                @endif
                                </td>
                                <td> {{ $s->vehicle_reg }} </td>
                                                            <td> {{ number_format($s->bill_amount, 2) }} </td>
                                <td>
                                   {{ number_format(($s->bill_amount - $s->paid_amount), 2) }}
                                   </td>
                                </td>
                            </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="8"> No Data Found </td>
                               </tr>
                            @endif
                        </tbody>
              <!-- <tfooter> -->
             <tr style="background: #FAF6F6;">
                 <td colspan="3" style="text-align: right"> Total Amount &nbsp;&nbsp; : &nbsp;&nbsp; </td>
                 <td> {{ number_format($total_charges, 2) }}</td>
                 <td>{{ number_format(($total_charges - $total_credit), 2) }}</td>
            </tr>
           <!-- </tfooter> -->
            </table>
       @elseif ($payment == 3)
       <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc; background: #ddd;">
                                <td>Date </td>
                                <td>Doc. No </td>
                                <td>Description</td>
                               
                                <td>Charges </td>
                               <td> Credit </td>
                                <td>Debit </td>
                                <td>Balance </td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($sales) > 0)
                            @foreach($sales as $s)
                            <tr  style="border-bottom: 1px solid #D9FAF9">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                @if ($s->invoice_number < 10)
                                SI {{ "00000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100)
                                SI {{ "0000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 1000)
                                SI {{ "000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 10000)
                                SI {{ "00".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100000)
                                SI {{ "0".$s->invoice_number }}
                                @else 
                                SI {{ $s->invoice_number }}
                                @endif
                                </td>
                                <td> {{ $s->vehicle_reg }} </td>
                             
                                <td> {{ number_format($s->bill_amount, 2) }} </td>
                                <td>
                                   {{ number_format(($s->bill_amount - $s->paid_amount), 2) }}
                                   </td>
                                 <td> {{ number_format(($s->bill_amount -$s->paid_amount), 2) }} </td>
                            </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="8"> No Data Found </td>
                               </tr>
                            @endif
                        </tbody>
              <!-- <tfooter> -->
             <tr style="background: #FAF6F6;">
                 <td colspan="4" style="text-align: right"> Total Amount &nbsp;&nbsp; : &nbsp;&nbsp; </td>
                 <td> {{ number_format($total_charges, 2) }}</td>
                 <td>{{ number_format(($total_charges - $total_credit), 2) }}</td>
                 <td>{{ number_format(($total_charges - $total_credit), 2) }}</td>
            </tr>
           <!-- </tfooter> -->
            </table>
        @elseif ($payment == null)
        <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc; background: #ddd;">
                                <td>Date </td>
                                <td>Doc. No </td>
                                <td>Description</td>
                               
                                <td>Charges </td>
                                <td>Credit </td>
                                <td>Debit </td>
                                <td>Balance </td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($sales) > 0)
                            @foreach($sales as $s)
                            <tr  style="border-bottom: 1px solid #D9FAF9">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                @if ($s->invoice_number < 10)
                                SI {{ "00000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100)
                                SI {{ "0000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 1000)
                                SI {{ "000".$s->invoice_number }}
                                @elseif ($s->invoice_number < 10000)
                                SI {{ "00".$s->invoice_number }}
                                @elseif ($s->invoice_number < 100000)
                                SI {{ "0".$s->invoice_number }}
                                @else 
                                SI {{ $s->invoice_number }}
                                @endif
                                </td>
                                <td> {{ $s->vehicle_reg }} </td>
                                <td> {{ number_format($s->bill_amount, 2) }} </td>
                                <td> {{ number_format($s->paid_amount, 2) }} </td>
                                <td> {{ number_format(($s->bill_amount - $s->paid_amount), 2) }}</td>
                                 <td> {{ number_format(($s->bill_amount -$s->paid_amount), 2) }} </td>
                            </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="8"> No Data Found </td>
                               </tr>
                            @endif
                        </tbody>
              <!-- <tfooter> -->
             <tr style="background: #FAF6F6;">
                 <td colspan="3" style="text-align: right"> Total Amount &nbsp;&nbsp; : &nbsp;&nbsp; </td>
                 <td> {{ number_format($total_charges, 2) }}</td>
                 <td>{{ number_format($total_credit, 2) }}</td>
                 <td>{{ number_format(($total_charges - $total_credit), 2) }}</td>
                 <td>{{ number_format(($total_charges - $total_credit), 2) }}</td>                 
            </tr>
           <!-- </tfooter> -->
            </table>
       @else
       @endif
      </main>
        </div> 
    <div>
</div>

</body>
</html>
