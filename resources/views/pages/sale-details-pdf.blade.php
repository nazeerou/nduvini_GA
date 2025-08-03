<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Sales Invoice') }}</title>
   <style>
     
      h4 {
          text-align: center;
      }
      tr th {
           background: #ddd;
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

        #sales_report td, #sales_report th {
        border: 1px solid #ffc;
        padding: 8px 0 8px 8px;
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
            border: 1px solid #010;
        }
        #rpt h4 {
            text-align: right;
        }
          div .logo,  h2 , h3 {
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
        <h2> {{ $settings[0]->business_name }} </h2>
        <h3> {{ $settings[0]->address }} </h3>    
        </div>

       <h4> BILL DETAILS  </h4> <br/>
        <p> CLIENT NAME : 
        @foreach ($client_name as $w)
                {{ $w->client_name }}  - {{ $w->place}}
         @endforeach
     </p>
     <p> REF No# :  {{ $id }}
       <div id="line"></div>
       <span id="rpt"><h4> 
        </h4></span>
    </div>
    <div class="container table-responsive">
    <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Item Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Unit Selling Price</th>
                                <th>Qty </th>
                                <th>Total Amount </th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{number_format( $product->selling_price, 2) }} </td>
                                <td>{{ $product->qty}}</td>
                                <td> {{number_format($product->total_sales, 2) }} </td>   
                            </tr>
                          @endforeach  
                                <tr>
                                 <td colspan="6" align="right">
                                     <strong> Sub Total Bill  : </strong>
                                    </td>
                                    <td>  
                                     {{ number_format($total_sales, 2) }} 
                                    </td>
                                     
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                      VAT Amount (18%) &nbsp; :
                                    </td><td>  
                                          {{ number_format($vat_calculations, 2) }}
                                      </td>
                                     
                                </tr>
                                <tr>
                                 <td colspan="6" align="right">
                                     <strong> Grand Total Bill Amount </strong>
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($total_sales + $vat_calculations), 2) }}
                                      </td>
                                     
                                </tr>
                            </tbody>
                        </table>
                        </div> 

    <div>
        <br/>
       <div class="footer">
       <div class="footer_">
       <p> Printed On:  <?php echo date('d M Y : h:i:s', strtotime("+3 GMT")); ?> </p>
      </div>
         <div class="left">
       <p> Printed By : <strong>  {{ Auth::user()->fname }} {{ Auth::user()->lname }} </strong>   
       </p>
            
      </div>
     
       </div>
       
    </div>
</div>
</body>
</html>