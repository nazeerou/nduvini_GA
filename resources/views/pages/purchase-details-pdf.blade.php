<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Purchase Invoice') }}</title>
   <style>
      tr {
           /* border: 1px solid #ddd; */
           padding: 8px;
      }
      h4 {
          text-align: center;
      }
      tr th {
           background: #ddd;
           padding: 6px;
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

        #sales_report td, #sales_report th {
        border: 1px solid #dee;
        padding: 8px 4px 8px 8px;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
        padding-top: 4px;
        padding-bottom: 4px;
        text-align: left;
        }
        .footer {
          position: fixed;
          bottom: 20px;
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
        .logo {
          text-align: center;
        }
        #title {
          font-size: 1.3em;
          font-weight: 300;
        }
   </style>

</head>
<body>
    <div class="header">
    <div class="logo">
          <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">    
         <div id="title"> {{ $settings[0]->business_name }}  </div>
        </div>
        
       <h4> PURCHASE INVOICE  </h4> <br/>
       <p> SUPPLIER NAME : {{ $supplier_name[0]->supplier_name }}</p>
    
     <p> INVOICE No# :  {{ $id }}</p>
       <div id="line"></div>
       <span id="rpt"><h4> 
        </h4></span>
    </div>
    <div class="container table-responsive">
    <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Part Name</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Part Number</th>
                                <th>Unit Purchase Price</th>
                                <th>Qty </th>
                                <th>Sub Total </th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->make }} </td>
                                <td> {{ $product->model }} </td>
                                <td> {{ $product->part_number }} </td>
                                <td> {{number_format( $product->purchase_price, 2) }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td> {{number_format( $product->total_purchase, 2) }} </td>  
                            </tr>
                          @endforeach  
                               <tr>
                                 <td colspan="7"> <h5 align="right">
                                      @if($product->vat_type > 0) 
                                      Sub Total Amount
                                      &nbsp; : </h5>
                                      @else
                                      Sub Total Amount
                                      @endif
                                    </td><td>  
                                        @if($product->vat_type > 0)
                                          {{ number_format($total_purchases,2) }}
                                            @else
                                            {{ number_format($total_purchases, 2) }} 
                                            @endif
                                      </td>
                                </tr>
                                <tr>
                                 <td colspan="7" align="right"> 
                                      @if($product->vat_type > 0) 
                                      VAT Amount (18%)
                                      &nbsp; :
                                      @else
                                      VAT Amount (18%)
                                      @endif
                                    </td><td>  
                                          @if($product->vat_type == 1)
                                          {{ number_format($vat_calculations, 2) }}
                                            @else
                                            {{ number_format($vat_calculations, 2) }} 
                                            @endif
                                      </td>
                                </tr>
                                <tr>
                                 <td colspan="7"> <h5 align="right">
                                      @if($product->vat_type > 0) 
                                      Grand Total Amount
                                      &nbsp; :
                                      @else
                                      Grand Total Amount
                                      @endif
                                    </td><td>  
                                        @if($product->vat_type > 0)
                                          {{ number_format(($total_purchases + $vat_calculations), 2) }}
                                            @else
                                            {{ number_format($total_purchases, 2) }} 
                                          @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                  </div> 
               <div>
           <br/>
       <div class="footer">
       <div class="footer_">
       <p> Printed On:  <?php echo date('d  M,  Y', strtotime("+3")); ?> </p>
      </div>
         <div class="left">
       <p> Printed By : <strong>  {{ Auth::user()->fname }} {{ Auth::user()->lname }} </strong> </p>
      </div>
     
       </div>
       
    </div>
</div>
</body>
</html>