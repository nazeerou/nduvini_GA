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
           /* background: #eee; */
           padding-top: 10px;
           font-size: 13px;
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
        width: 35%;
        }
        #total_estimate td, #sales_report th {
        /* border: 1px solid #ddd; */
        padding: 8px 0 8px 8px;
        }

        #sales_report td, #sales_report th {
        /* border: 1px solid #ddd; */
        padding: 6px 0 5px 5px;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding-top: 8px;
        padding-bottom: 8px;
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

body 1{
  /* The image used */
  background-image: url("assets/images/logo_nduvini.jpeg");

  /* Full height */
  height: 90%;

  /* Center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        height: 100vh;
        opacity: 0.5;}
   </style>

</head>
<body>
 <div class="header">
 <div class="logo">
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 700px;" class="center img-thumbnail img-responsive profile-pic">    
 <!-- <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">     -->
</div>
<div id="line"></div>

<table style="border: 0px" width="700px">
<tr>
    <td width="400px">
       <p> {{ $clients[0]->client_name ?: '' }} </p>
       <p> {{ $clients[0]->address ? : ''}} </p>
       <p> {{ $clients[0]->place ? : ''}} </p>
       <p></p>
       <p></p>
       <p></p>
    </td>
    <td>
    <p><strong>STATEMENT</strong> </p>
    <p>Period : </p> 
    <p>Account No : </p> 
    <p>Account Terms : </p>     
    <p>Page : </p>     
    </td>
    <td style="text-align: right"> 
    <p> </p>
    <p> {{ $startdate }} TO  {{ $enddate }} </p>    
    <p> 
        @if (count($sales) > 0) 
            @if ($sales[0]->account_no < 10)
            {{ $sales[0]->account_prefix ? : '' }}{{ '00'.$sales[0]->account_no ? : '' }}
            @elseif ($sales[0]->account_no < 100)
            {{ $sales[0]->account_prefix ?: '' }}{{ '0'.$sales[0]->account_no ? : '' }}
            @else 
            {{ $sales[0]->account_prefix ? : '' }}{{ $sales[0]->account_no ? : '' }}
            @endif
        @else 
          NULL
        @endif
    </p>    
    <p> </p>
    <p> </p>
    <p></p>
  </td>
</tr>
</table>
 <div id="line"></div>
 <br/><br/>
<span id="rpt"></div>

             <table id="sales_report" class="table table-bordered">
                      <thead>
                            <tr style="border: 1px solid #ccc">
                                <td>Date </td>
                                <td>Document </td>
                                <td>Description</td>
                                <td></td>
                                <td>Reference </td>
                                <td>Charges </td>
                                <td>Paid </td>
                                <td>Unpaid </td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(count($sales) > 0)
                            @foreach($sales as $s)
                            <tr  style="border-bottom: 1px solid #ddd">
                                <td> {{ $s->created_date }} </td>
                                <td>
                                @if ($s->invoice_number < 10)
                                SI {{ '00000'.$s->invoice_number }}
                                @elseif ($s->invoice_number < 100)
                                SI {{ '0000'.$s->invoice_number }}
                                @elseif ($s->invoice_number < 1000)
                                SI {{ '000'.$s->invoice_number }}
                                @elseif ($s->invoice_number < 10000)
                                SI {{ '00'.$s->invoice_number }}
                                @elseif ($s->invoice_number < 100000)
                                SI {{ '0'.$s->invoice_number }}
                                @else 
                                SI {{ $s->invoice_number }}
                                @endif
                                </td>
                                <td> {{ $s->vehicle_reg }} </td>
                                <td> {{ $s->make }} {{ $s->model }}  </td>
                                <td> {{ '' }}  </td>
                                <td> {{ number_format($s->bill_amount, 2) }}</td>
                                <td> {{ number_format($s->paid_amount, 2) }} </td>
                                <td>
                                    @if($s->bill_amount > $s->paid_amount)
                                     {{ number_format($s->bill_amount - $s->paid_amount,2) }}
                                     @else 
                                     0.00
                                     @endif
                                </td>
                            </tr>
                            @endforeach
                            @else 
                                <tr>
                                    <td style="padding-top: 20px; text-align: center;" colspan="8"> No Data Found </td>
                            </tr>
                            @endif
                        </tbody>
             </table>
       <br/><br/>
        </div> 
    <div>
</div>

</body>
</html>