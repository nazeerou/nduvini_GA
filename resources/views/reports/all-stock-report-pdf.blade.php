<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Stock Report') }}</title>

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
           padding: 1px;
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

        #sales_report td {
        border: 1px solid #ddd;
        padding: 4px;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding: 5px;
        text-align: left;
        font-size: 0.9em;
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
        <div class="header">
         <div class="logo">
          <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">    
      </div>
    <h4> STOCK CLOSING AND BALANCES FOR THE YEAR  <br/>  {{ $previous  }} / {{ $current }}</h4>

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
                                <th> # </th>
                                <th>ITEM NAME</th>
                                <th> SELLING PRICE</th>
                                <th> ITEM ISSUED (SOLD) </th>
                                <th> BALANCE (ONSTOCK) </th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($sale_items as $key=>$p)
                                <tr>
                                <td width="10px">{{ $key+1 }} </td>
                                <td width="250px"> {{ strtoupper($p->item_name) }} ( {{strtoupper($p->title )}}-{{ strtoupper($p->model) }})</td>
                                <td width="100px"> {{ number_format($p->selling_price, 2) }} </td>
                                <td width="100px"> {{ $p->item_sold }} {{ strtoupper($p->sale_unit) }} </td>
                                <td width="100px"> {{ $p->quantity }}  {{ $p->quantity ? $p->sale_unit : '' }} </td>
                                </tr>
                                @endforeach
                            </tbody>
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