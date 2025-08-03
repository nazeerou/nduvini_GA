<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Suppliers Payment Report') }}</title>

   <style>
      tr {
           border: 1px solid #ddd;
           padding: 2px;
      }
      h4, h2 {
          text-align: center;
      }
      tr th {
           background: #ddd;
           padding: 2px;
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
   </style>

</head>
<body>
    <div class="header">
        <h2> {{ $settings[0]->business_name }} </h2>

       <h4> Suppliers Payment Report For the Year <br/>  {{ $previous_year }} / {{ $current_year }}  </h4>
       <div id="line"></div>
       <br/>     
    </div>
    <div class="container table-responsive">
           <div class="row">
                        <div class="col-md-12">
                            <div id="display_results">
                            <table id="sales_report" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th>SUPPLIER NAME</th>
                                    <th>TOTAL AMOUNT </th>
                                    <th>PAID AMOUNT</th>
                                    <th>OWED AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $key=>$p)
                                <tr>
                                <td>{{ $key+1 }} </td>
                                <td> {{ strtoupper($p->supplier_name) }} </td>
                                <td>{{ number_format($p->total_purchases, 2) }} </td>
                                <td>{{ number_format($p->paid_amount, 2) }} </td>
                                <td style="color: red">{{ number_format((($p->total_purchases) - ($p->paid_amount)), 2) }} </td>
                                </tr>
                                @endforeach
                                <tr>
                                <td></td>
                                <td align="right"> TOTAL  :  &nbsp; </td>
                                <td> 
                                    @foreach($total_purchases_amount as $p)
                                        {{ number_format($p->total_purchases, 2) }}
                                    @endforeach
                                </td>
                                <td> {{ number_format($total_paid_amount, 2) }} </td>
                                <td> 
                                @foreach($total_purchases_amount as $p)
                                        {{ number_format(($p->total_purchases - $total_paid_amount), 2) }}
                                    @endforeach
                                </td>
                            </tr>
                            </tbody>
                            </table>
                            </div>
                        </div> 

    <div>
        <br/>
       <div class="footer">
       <p> Prepared By : <strong> {{ AUth::user()->fname }} </strong> </p>
       </div>
    </div>
</div>
</body>
</html>