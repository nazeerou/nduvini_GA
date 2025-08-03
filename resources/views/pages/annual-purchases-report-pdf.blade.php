<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Annual Purchase Report') }}</title>

   <style>
      tr {
           border: 1px solid #ddd;
           padding: 3px;
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

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 8px;
        }

        #sales_report tr:last-child td {
            height: 50px;
            background-color: #f2f2f2;
        }
        #sales_report tr:nth-child(even){background-color: #f2f2f2;}

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding-top: 2px;
        padding-bottom: 2px;
        text-align: left;
        text-transform: uppercase;
        }
        .footer {
        position:absolute; 
        bottom:0px;
        }
        #line {
            border: 1px solid #ddd;
        }
        #rpt h4 {
            text-align: right;
        }
        #itemName {
            font-size: 12px;
        }
         div {
          /*display: block;*/
          /*margin-left: auto;*/
          /*margin-right: auto;*/
          /*width: 50%;*/
          text-align: center;
       }
       .title_ {
        font-size: 1.3em;
    }
   </style>

</head>
<body>
    <div class="header">
        <div class="logo">
          <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file  }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">    
      </div>
      <div class="title_"> {{ $settings[0]->business_name }} </div>

       <h4> ANNUAL PURCHASES REPORT </h4>
       <h4> ( {{ $enddate }} ) </h4>
       <div id="line"></div>
       <span id="rpt"><h4> Total Inventory Purchased Value:  
       @foreach($total_inventory_purchases_value as $v) 
              {{ number_format(($v->total_purchase_amount+$v->vat_amount),2) }}
          @endforeach       
    </h4></span>
    </div>
    <div class="container table-responsive">
           <div class="row">
                        <div class="col-md-12">
                            <div id="display_results">
                            <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th  width="20px"> # </th>
                                <th>Supplier Name </th>
                                <th width="100">Invoice Number</th>
                                <th width="100">Invoice Date</th>
                                <th width="150px">Invoice Amount (VAT incl.)</th>
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
                                     {{number_format(($p->total + $p->vat_amount), 2) }} 
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
        <p> Prepared By : <strong> {{ Auth::user()->fname }} {{ Auth::user()->lname }} </strong> </p>
       </div>
    </div>
</div>
</body>
</html>