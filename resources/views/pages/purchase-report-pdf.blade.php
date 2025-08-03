<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Purchase Report') }}</title>

   <style>
      tr {
           border: 1px solid #ddd;
           padding: 2px;
           text-transform: uppercase;
      }
      h4, h2 {
          text-align: center;
      }
      tr th {
           background: #ddd;
           padding: 4px;
           font-size: 13px;
      }
      h5 {
          text-align: right;
      }
      table {
          font-size: 14px;
      }
      #sales_report {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #sales_report td, #customers th {
        border: 1px solid #ddd;
        padding: 8px;
        }

        #sales_report tr:nth-child(even){background-color: #f2f2f2;}

        #sales_report tr:last-child td {
            height: 50px;
            background-color: #f2f2f2;
        }
        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding-top: 10px;
        padding-bottom: 10px;
        text-align: left;
        /* background-color: #7093dd; */
        /* color: white; */
        }
        .footer {
        position:absolute; 
        bottom:0px;
        }
        #rpt h4 {
            text-align: right;
        }
        #line {
            border: 1px solid #ddd;
        }
        div {
          /*display: block;*/
          /*margin-left: auto;*/
          /*margin-right: auto;*/
          /*width: 50%;*/
          text-align: center;
       }
   </style>

</head>
<body>
    <div class="header">
        <div class="logo">
        <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">    
      </div>
        <div class="title_"> {{ $settings[0]->business_name }} </div>

       <h4> Purchases Report  </h4>
       <h4> From Date:   {{ $startdate }}   To Date:   {{ $enddate }}   </h4>
       <div id="line"></div>
       <span id="rpt"> <h4> 
    </h4></span>
    </div>
    <div class="container table-responsive">
           <div class="row">
                    <div class="col-md-12">
                     <div id="sales_report">
                        <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Supplier Name </th>
                                <th width="120">Invoice Number</th>
                                <th width="100">Invoice Date</th>
                                <th width="200px">Invoice Amount (VAT incl.)</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $purchase)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $purchase->supplier_name }}</td>
                                <td> 
                                {{ $purchase->invoice_number }}
                                </td>
                                <td>{{ $purchase->created_date}}</td>    
                                <td> {{number_format(($purchase->total + $purchase->calculated_vat_amount), 2) }}</td>
                            </tr>
                          @endforeach  
                              <tr>
                                 <td colspan="4" align="right"> <span> TOTAL AMOUNT  &nbsp; : </span> </td>
                                 <td> 
                                     @foreach($total_purchases as $p)
                                     {{number_format(($p->total + $p->calculated_vat_amount), 2) }} 
                                     @endforeach
                                     </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> 
           <div>
        <br/>
       <div class="footer" style="text-align: left;">
       <p> Printed By : <strong> {{ Auth::user()->fname }} {{ Auth::user()->lname }} </strong> </p>
       </div>
    </div>
   </div>
</body>
</html>