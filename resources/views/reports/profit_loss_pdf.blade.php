<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('PROFIT & LOSS REPORT') }}</title>

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
        padding: 12px;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:last-child td {
            height: 30px;
            background-color: #f2f2f2;
        }

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report tr {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        text-transform: uppercase;
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
       <h4> Profit and Loss Report  </h4>
       <h4> FROM :   {{ $startdate }} TO  :   {{ $enddate }}   </h4>
       <div id="line"></div>
       <span id="rpt"><h4>   
         </h4></span>
    </div>
    <div class="container table-responsive">
           <div class="row">
                    <div class="col-md-12">
                        <div id="display_results">
                        <table id="sales_report" class="table table-bordered">
                         <thead style="background-color: #dddccc;">
                        </thead>

                        <tbody>
                            <tr>
                                <td>TOTAL SALES : </td>
                                <td> 
                                    @foreach($total_sales as $s)
                                     {{ number_format($s->total_sales, 2) }}   
                                     @endforeach  
                                </td>       
                            </tr>
                            <tr>
                                <td> TOTAL ITEM(S) SOLD  : </td>
                            <td> 
                            @foreach($total_quantity as $q)
                                     {{ number_format($q->quantity) }}   
                            @endforeach    
                            </td>
                            </tr>
                            <tr>
                                <td> LOSS  : </td>
                            <td> 
                            @foreach($total_loss as $p)
                               @if($p->loss < 0)
                                   <span> 0 </span>
                               @else
                                     {{ number_format($p->loss) }} 
                              @endif  
                            @endforeach   
                            </td>
                            </tr>
                            <tr>
                                <td> PROFIT : </td>
                            <td> 
                            @foreach($total_profits as $p)
                               @if($p->profit > 0)
                                     {{ number_format($p->profit) }} 
                                @else 
                                   <span> 0 </span>
                                @endif
                            @endforeach     
                            </td>
                            </tr>
                            </tbody>
                        </table>
                        </div>
                </div> 

    <div>
        <br/><br/><br/><br/><br/>
       <div class="footer">
       <p> Printed By : <strong> {{ Auth::user()->fname }}   </strong> </p>
       </div>
    </div>
</div>
</body>
</html>