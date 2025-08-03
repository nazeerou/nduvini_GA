<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Tax Invoice') }}</title>
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
        #total_estimate td, #sales_report th {
        border: 1px solid #ddd;
        padding: 6px 0 6px 13px;
        }

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 4px 0 0px 15px;
        font-size: 0.9em;

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

main {
        background-image: url("assets/images/logo_nduvini.jpeg");
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
            bottom: -75px; 
            left: 0px; 
            right: 0px;
            height: 115px; 
            font-size: 20px !important;
            background-color: red;
            color: white;
            width: 100%;
            /* text-align: center; */
            /* line-height: 35px; */
        }
        footer .page-number {
            /* text-align: center; */
        }
        .page-number:before {
            content: "Page  " counter(page);
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
        table p {
            line-height: 10px;
        }
  .branch {
       font-size: 0.8em;
       padding: 3px 0 2px 26px;
      }          
   </style>
   </style>

</head>
<body>
<header>
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 760px;" class="center img-thumbnail img-responsive profile-pic">    

<div class="branch"> 
@if(Auth::user()->branch_id == 1)
    HEAD OFFICE :  Dar es Salaam 
@elseif (Auth::user()->branch_id == 2)
   BRANCH : TABORA 
@elseif (Auth::user()->branch_id ==3)
   BRANCH : DODOMA 
@else
@endif
</div>
<div id="line"></div>

<table style="border: 0px" width="740px">
<tr>
    <td width="490px">
       @if($client_name[0]->client_name != NULL)
       <p> {{ $client_name[0]->client_name }} </p>
       <p> {{ $client_name[0]->address }} </p>
       <p> {{ $client_name[0]->place }} </p>
       <p></p>
       <p> TIN :  {{ $client_name[0]->tin }}  VRN :  {{ $client_name[0]->vrn }}  </p>
       @else
       {{ $sales[0]->customer_name }}
           <p></p><p></p><p></p>
        @endif
       <p></p><p></p><p></p>
    </td>
    <td>
    <p><strong>TAX INVOICE</strong> </p>
    <p>Invoice Date : </p> 
    <p>Account No : </p> 
    <p>Order Ref : </p>    
    <p>Payment Status : </p>  
    <!-- <p>Payment Method : </p>      -->
    </td>
    <td style="text-align: right">
    <p> <strong> 
    @if ($sales[0]->invoice_number < 10)
        {{ '00000'.$sales[0]->invoice_number }}
        @elseif ($sales[0]->invoice_number < 100)
        {{ '0000'.$sales[0]->invoice_number }}
        @elseif ($sales[0]->invoice_number < 1000)
        {{ '000'.$sales[0]->invoice_number }}
        @elseif ($sales[0]->invoice_number < 10000)
        {{ '00'.$sales[0]->invoice_number }}
        @elseif ($sales[0]->invoice_number < 100000)
        {{ '0'.$sales[0]->invoice_number }}
        @else 
        {{ $sales[0]->invoice_number }}
        @endif
    </strong> </p> 
    <p> {{ $sales[0]->created_date }} </p>    
    <p> 
    @if ($sales[0]->account_no < 10)
        {{ $sales[0]->account_prefix }}{{ '00'.$sales[0]->account_no }}
        @elseif ($sales[0]->account_no < 100)
        {{ $sales[0]->account_prefix }}{{ '0'.$sales[0]->account_no }}
        @else 
        {{ $sales[0]->account_prefix }}{{ $sales[0]->account_no }}
        @endif
    </p>    
    <p> {{ $sales[0]->reference }}</p>    
    <p> UNPAID </p>
    <p></p>
  </td>
</tr>
</table>
</header>
 <br/><br/><br/></br/><br/><br/><br/>
 <span id="rpt"><h4> </h4></span>
     <div id="line"></div>
     <br/>
<footer>
<!-- <div class="footer" style="background: #ff0000; color: #fff"> -->
<!-- <div id="line"></div> -->
<table style="border: 0px; padding: 0; margin: 0;">
   <tr>
   <td>
    <ul> 
        <li> HEAD OFFICE :  Temeke District - Chang'ombe </li>
        <li> P.o Box 45610 DSM </li> 
        <li> Tel: +255 739275223 |
         Fax +255 22 2860276 </li>
        <li> Phone: +255 754 275223 <li>
        <li> Email: nduvin@hotmail.com <li> 
       </ul>
  </td>
    <td>
       <ul> 
        <li> Branch Office : Singida Road- Kizota </li>
        <li> P.o Box 530 DODOMA </li>
        <li> Tel: +255 738692607 </li>
        <li> Phone: +255 753 915234 <li>
        <li> Email: dodoma@nduviniautoworks.co.tz <li> 
       </ul>
    </td>
    <td>
        <ul> 
        <li> Branch Office :  Tabora </li>
        <li> P.o Box 45610 Tabora </li>
        <li> Tel: +255 738310148 </li>
        <li> Phone: +255 754 275223 <li>
        <li> Email: tabora@nduviniautoworks.co.tz <li> 
       </ul>  
      </td>
   </tr>
</table>
<!-- </div> -->
</div>
</footer>
          <main>
             <table id="sales_report" class="table table-striped table-bordered vehicle_info">
                      <thead>
                             <tr>
                                <th>Vehicle Reg </th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Chassis No.</th>
                                <th>Milleage </th>
                          </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> {{ $sales[0]->vehicle_reg }} </td>
                                <td> {{ $sales[0]->emake }} </td>
                                <td> {{ $sales[0]->emodel }} </td>
                                <td> {{ $sales[0]->chassis }} </td>
                                <td> {{ $sales[0]->milleage }} </td>
                            </tr>
                        </tbody>
                   </table>
            <br/><br/>
        <div class="container table-responsive">
               <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Parts</th>
                                <th>Qty </th>
                                <th>Unit </th>
                                <th>D </th>
                                <th>Sub Total </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td width="400px"> {{ strtoupper($product->item_name) }} </td>
                                <td> {{ $product->qty }} 
                                  @if($product->qty > 1)
                                   {{ strtoupper($product->purchase_unit) }}S
                                  @else
                                   {{ strtoupper($product->purchase_unit) }}
                                   @endif
                                </td>
                                <td> {{number_format( $product->selling_price, 2) }} </td>
                                <td> {{ $product->discount ? : ''  }} {{ $product->discount ? '%' : ''  }}    </td>
                                <td> {{number_format($product->total_sales, 2) }} </td>   
                            </tr>
                          @endforeach  
                    </tbody>
                    </table>
                    <br/>
                    <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Labour </th>
                                <th>Qty </th>
                                <th>Unit </th>
                                <th>D </th>
                                <th>Sub Total </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($labours as $key => $l)
                            <tr>
                                <td width="400px"> {{ strtoupper($l->labour_name) }} </td>
                                <td>  </td>
                                <td> {{number_format(($l->total_amount), 2) }}  </td>
                                <td></td>
                                <td> {{number_format(($l->total_amount), 2) }} </td>   
                            </tr>
                          @endforeach  
                    </tbody>
                </table>
                  <br/><br/>
                        
                  @if (count($sales) % 13 == '0' OR count($sales) % 14 == '0' OR count($sales) % 15 == 0 OR count($sales) % 15 == '0' OR count($sales) % 16 == '0' OR count($sales) % 17 == 0 OR count($sales) % 18 == '0' OR count($sales) % 19 == '0' OR count($sales) % 20 == 0 OR
                    count($sales) % 21 == '0' OR count($sales) % 22 == '0' OR count($sales) % 23 == 0 OR count($sales) % 24 == '0' OR count($sales) % 25 == '0' OR count($sales) % 26 == 0 OR count($sales) % 27 == '0' OR count($sales) % 28 == '0' OR count($sales) % 29 == 0 OR count($sales) % 30 == '0' OR count($sales) % 31 == '0' ) <!-- Apply page break after every 10 rows -->
                    <div class="page-break"></div>
                    <br/>
                   
                <div id="footer_details">
                    <div class="account_details" style="color: #000;">
                    @if($sales[0]->account_number > 0)
                    <table>
                    <tr<td> BANK DETAILS : <br/> {{ $sales[0]->bank_name ? : '' }}</td></tr>
                    <tr<td> ACCOUNT NAME : {{ $sales[0]->account_name ? : '' }}</td></tr>
                    <tr<td> ACCOUNT NO:   {{ $sales[0]->account_number ? : '' }} </td></tr>
                    <tr<td> BRANCH :  {{ $sales[0]->branch_name ? : '' }}</td></tr>
                    <tr<td> SWIFT CODE : {{ $sales[0]->swift_code ? : '' }}</td></tr>
                  </table>
                  @else 
                  @endif
                    </div>
                    <div class="total_summations">
                    <table id="total_estimate"  class="table table-striped table-bordered" align="right">
                           <thead>
                              @if($total_discounts != '')
                               <tr>
                                 <td> <strong> Net Discount : </strong> </td>
                                    <td> {{ number_format($total_discounts, 2) }} </td>
                                </tr>
                                @else  {{ '' }} @endif 
                                 <tr>
                                 <td> Parts : </td>
                                    <td> {{ number_format($total_sales, 2) }} </td>
                                </tr>
                                <tr>
                                 <td> Labour : </td>
                                    <td> {{ number_format($total_labours, 2) }} </td>
                                </tr>
                                <tr>
                                 <td>
                                     <strong> Sub Total : </strong>
                                    </td>
                                    <td width="100px"> {{ number_format(($total_sales + $total_labours), 2) }} </td>
                                </tr>
                              @if (!empty($temesa_fee) && $temesa_fee > 0)
                                    <tr>
                                        <td>TEMESA Fee (8%):</td>
                                        <td>{{ number_format($temesa_fee, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                 <td>
                                      VAT Amount (18%)  :
                                    </td><td>  
                                          {{ number_format($vat_charges, 2) }}
                                      </td>
                                </tr>
                                <tr style="background: #eee">
                                 <td>
                                     <strong> Total   </strong>
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($grand_total_amount), 2) }}
                                      </td>  
                            </tr>
                     </thead>
                 </table>
                    @else 
                    
                  <br/><br/>
                        
                <div id="footer_details">
                    <div class="account_details" style="color: #000;">
                    @if($sales[0]->account_number > 0)
                    <table>
                    <tr<td> BANK DETAILS : <br/> {{ $sales[0]->bank_name ? : '' }}</td></tr>
                    <tr<td> ACCOUNT NAME : {{ $sales[0]->account_name ? : '' }}</td></tr>
                    <tr<td> ACCOUNT NO:   {{ $sales[0]->account_number ? : '' }} </td></tr>
                    <tr<td> BRANCH :  {{ $sales[0]->branch_name ? : '' }}</td></tr>
                    <tr<td> SWIFT CODE : {{ $sales[0]->swift_code ? : '' }}</td></tr>
                  </table>
                  @else 
                  
                  @endif
                    </div>
                    <div class="total_summations">
                    <table id="total_estimate"  class="table table-striped table-bordered" align="right">
                           <thead>
                              @if($total_discounts != '')
                               <tr>
                                 <td> <strong> Net Discount : </strong> </td>
                                    <td> {{ number_format($total_discounts, 2) }} </td>
                                </tr>
                                @else  {{ '' }} @endif 
                                 <tr>
                                 <td> Parts : </td>
                                    <td> {{ number_format($total_sales, 2) }} </td>
                                </tr>
                                <tr>
                                 <td> Labour : </td>
                                    <td> {{ number_format($total_labours, 2) }} </td>
                                </tr>
                                <tr>
                                 <td>
                                     <strong> Sub Total : </strong>
                                    </td>
                                    <td width="100px"> {{ number_format(($total_sales + $total_labours), 2) }} </td>
                                </tr>
                               @if (!empty($temesa_fee) && $temesa_fee > 0)
                                    <tr>
                                        <td>TEMESA Fee (8%):</td>
                                        <td>{{ number_format($temesa_fee, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                 <td>
                                      VAT Amount (18%)  :
                                    </td><td>  
                                          {{ number_format($vat_charges, 2) }}
                                      </td>
                                </tr>
                                <tr style="background: #eee">
                                 <td>
                                     <strong> Total   </strong>
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($grand_total_amount), 2) }}
                                      </td>  
                            </tr>
                     </thead>
                 </table>
                    @endif
                    </div>
                </div>
             </div> 
         <div>
    </div>
</main>
</body>
</html>
