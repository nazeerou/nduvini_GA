<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Sales Item Report') }}</title>

   <style>
      tr {
           border: 1px solid #ddd;
           padding: 2px;
      }
      h4 , h2 {
          text-align: center;
      }
      tr th {
           background: #ddd;
           padding: 1px;
           font-size: 13px;
      }
      h5 {
          text-align: left;
      }
      table {
          font-size: 14px;
      }
      #sales_report {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #sales_report td {
        border: 1px solid #ddd;
        padding: 4px;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding: 5px;
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

    <h4> SALES AND STOCK BALANCES FOR THE YEAR  <br/>  {{ $previous  }} / {{ $current }}</h4>
    <h5> ITEM NAME : @foreach($item_name as $i) {{ $i->item_name }} @endforeach </h5>
    <h5> MODEL  :   @foreach($makes as $i) {{ strtoupper($i->model) }} @endforeach  </h5>
       <div id="line"></div>
       <br/>     
    </div>
    <div class="container">
           <div class="row">
                 <div class="col-md-12">
                    <div id="sales_report">
                     <table id="datatable" class="table table-striped table-bordered">
                        <thead style="background-color: #7093cc;">
                            <tr>
                                <th width="10px"> # </th>
                                <th width="100px">DATE</th>
                                <th width="120px">CLIENT NAME</th>
                                <th width="100px"> ISSUED TO </th> 
                                <th width="100px"> SOLD AT </th>
                                <th width="100px"> ISSUE(S) / QTY  </th>
                                <th width="90px"> BALANCE </th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($sale_items as $key=>$p)
                                <tr>
                                <td>{{ $key+1 }} </td>
                                <td> {{ $p->created_date }}</td>
                                <td> {{ strtoupper($p->client_name) }}</td>
                                <td> {{ strtoupper($p->vehicle_reg) }} </td>
                                <td> {{ number_format($p->selling_price, 2) }} </td>
                                <td> {{ $p->qty }} {{ $p->sale_unit }} </td>
                                <td> {{ $p->qty_balance }}  {{ $p->qty_balance ? '' : '-' }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tr style="background: #ddd;">
                                <td colspan="4">  </td>
                                <td align="right"> TOTAL  :  &nbsp; </td>
                                <td> {{ $total_item_sold }} </td> <td> {{ $total_item_onstocks[0]['quantity'] }} </td>

                            </tr>
                     </table>  
                    </div>
                </div> 

    <div>
        <br/>
       <div class="footer">
       <p> Prepared By : <strong>  {{ Auth::user()->fname }} </strong> </p>
       </div>
    </div>
</div>
</body>
</html>