<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Sales Report PDF') }}</title>

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

        #sales_report tr:last-child td {
            height: 50px;
            background-color: #f2f2f2;
        }

        #sales_report tr:hover {background-color: #ddd;}

        #sales_report th {
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
            border: 1px solid #ddd;
        }
        div {
          /*display: block;*/
          /*margin-left: auto;*/
          /*margin-right: auto;*/
          /*width: 50%;*/
          text-align: center;
       }
       #title_ {
          font-size: 1.3em;
       }
   </style>

</head>
<body>
    <div class="header">
        <div class="logo">
          <img src="{{ public_path().'/attachments/'.$settings[0]->logo_file }}" alt="User profile" style="height: 100px; width: 100px;" class="center img-thumbnail img-responsive profile-pic">    
      </div>
       <div id="title_"> {{ $settings[0]->business_name }} </div>
       <!-- <h3> {{ $settings[0]->address }} </h3> -->

       <h4> SALES REPORT  </h4>
       <h4> From Date:   {{ $startdate }} To Date:   {{ $enddate }}   </h4>
       <div id="line"></div>
       <span id="rpt"></span><br/>
    </div>
    <div class="container table-responsive">
           <div class="row">
                    <div class="col-md-12">
                        <div id="display_results">
                        <table id="sales_report" class="table table-striped table-bordered">
                         <thead style="background-color: #dddccc;">
                            <tr>
                                <th> # </th>
                                <th> Client Name </th>
                                <th> Vehicle Reg. </th>
                                <th> Date Sold</th>
                                <th> Bill Amount (VAT incl.)</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td width="10px">{{ $key+1 }}</td>
                                <td width="180px"> 
                                     {{ strtoupper($product->client_name) }} - {{ strtoupper($product->place) }}
                                </td>
                                <td width="110px">{{ $product->vehicle_reg }}</td>
                                <td width="110px">{{ $product->created_date }}</td>
                                <td width="150px"> {{ number_format($product->bill_amount, 2) }} </td>
                            </tr>
                             @endforeach  
                               <tr>
                                <td colspan="4" align="right"> <span> TOTAL AMOUNT &nbsp; : </span> </td>
                                <td> 
                                    {{ number_format($total_sales, 2) }}
                                </td>
                                </tr>
                            </tbody>
                        </table>
                            </div>
                        </div> 

    <div>
        <br/><br/><br/><br/><br/>
       <div class="footer" style="text-align: left;">
       <p> Prepared By : <strong> {{ Auth::user()->fname }} {{ Auth::user()->lname }} </strong> </p>
       </div>
    </div>
</div>
</body>
</html>