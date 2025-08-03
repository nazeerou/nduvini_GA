<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Labour Charge Report') }}</title>

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
       <h4> Labour Charge Report For the Year <br/> 
        {{ $previous_year }} / {{ $current_year }} 
      </h4>     
    </div>

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
                                    <th>CLIENT NAME</th>
                                    <th>CREATED DATE</th>
                                    <th>BILL NUMBER </th>
                                    <th>LABOUR CHARGE</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $key=>$sale)
                                <tr>
                                <td>{{ $key+1 }} </td>
                                <td> {{ strtoupper($sale->client_name) }} </td>
                                <td>{{ $sale->created_date }} </td>
                                <td>{{ $sale->bill_no }} </td>
                                <td>{{ number_format($sale->total_sales, 2) }} </td>
                                </tr>
                                @endforeach
                            <tr>
                                <td colspan="2"></td>
                                <td></td>
                                <td align="right"> TOTAL  :  &nbsp; </td>
                                <td> 
                                    @foreach($total_sales_amount as $p)
                                        {{ number_format($p->total_sales, 2) }}
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
       <p> Prepared By : <strong>  Regional Manager </strong> </p>
           TEMESA - {{ Auth::user()->place }} 
       </div>
    </div>
</div>
</body>
</html>