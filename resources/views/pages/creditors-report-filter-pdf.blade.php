<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Suppliers Report') }}</title>

   <style>
       tr {
           border: 1px solid #ddd;
           padding: 2px;
      }
     
      tr th {
           background: #dde;
           padding: 2px;
            text-transform: uppercase;
            font-size: 0.9em;
            font-family: 'Raleway', sans-serif;
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

        #sales_report tr:last-child td {
            height: 50px;
            /* background-color: #f2f2f2; */
        }

        #sales_report td, #customers th {
        border: 1px solid #ddd;
        padding: 8px;
        }

        #sales_report tr:nth-child(even){background-color: #f2f2f2;}

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
            border: 1px solid #010;
        }

        .header h4 {
            text-align: center;
        }
   </style>

</head>
<body>
    <div class="header">
    <h4> THE UNITED REPUBLIC OF TANZANIA </h4>
     <h4> Tanzania Electrical, Mechanical and Services Agency (TEMESA) </h4>
       <h4> Supplier Report For the Year <br/>  {{ $previous_year }} / {{ $current_year }}  </h4>
    </div>
           <h4> SUPPLIER NAME :  {{ $supplier_name->supplier_name }}</h4>
       <div id="line"></div>
       <br/>     
    <div class="container table-responsive">
           <div class="row">
                        <div class="col-md-12">
                            <div id="display_results">
                            <table id="sales_report" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> LPO NUMBER </th>
                                    <th> INVOICE NUMBER </th>
                                    <th>INVOICE AMOUNT </th>
                                    <th>PAID AMOUNT</th>
                                    <th>OWED AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $key=>$p)
                                <tr>
                                <td>{{ $key+1 }} </td>
                                <td> {{ strtoupper($p->lpo_number) }} </td>
                                <td> {{ strtoupper($p->invoice_number) }} </td>
                                <td>{{ number_format($p->total_purchases, 2) }} </td>
                                <td>{{ number_format($p->paid_amount, 2) }} </td>
                                <td style="color: red">{{ number_format((($p->total_purchases) - ($p->paid_amount)), 2) }} </td>
                                </tr>
                                @endforeach
                                <tr>
                                <td colspan="2"></td>
                                <td align="right"> TOTAL  :  &nbsp; </td>
                                <td> 
                                        {{ number_format(($total_purchases_amount + $total_vat_amount), 2) }}
                                </td>
                                <td> 
                                        {{ number_format($total_paid_amount, 2) }}
                                </td>
                                <td> 
                                        {{ number_format((($total_purchases_amount+$total_vat_amount)-$total_paid_amount), 2) }}
                                </td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                        </div> 

    <div>
        <br/>
       <div class="footer">
       <p> Prepared By : <strong>  Regional Manager </strong> </p>
           TEMESA - {{ Auth::user()->place }} 
       </div>
    </div>
</div>
</body>
</html>