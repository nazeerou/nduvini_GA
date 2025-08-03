<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Job Sheet') }}</title>
   <style>
     
      h4 {
          /* text-align: center; */
      }
      tr th {
           background: #eee;
           padding-top: 10px;
           font-size: 13px;
      }
      h5 {
          text-align: right;
          font-size: 1em;
      }
      table {
          font-size: 13px;
      }
      #sales_report, #vehicle-reg {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #total_estimate {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 35%;
        }
        #total_estimate td, #sales_report th {
        border: 1px solid #ddd;
        padding: 8px 0 8px 8px;
        }

        #vehicle-reg td, #vehicle-reg th {
        border: 1px solid #ddd;
        padding: 4px 0 4px 4px;
        text-align: center;
        }
        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 0px 0 0px 8px;
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
            border: 1px solid #ddd;
        }
        #rpt h4 {
            text-align: right;
        }
          
     .business_name {
          text-align: left;
          padding-left: 14px;
          font-size: 22px;
          font-weight: 300;
     }
     input[type="checkbox"] {
         display: inline-block;
         vertical-align: middle;
         cursor: pointer;
     }

body 1{
  /* The image used */
  background-image: url("assets/images/logo_nduvini.jpeg");

  /* Full height */
  height: 90%;

  /* Center and scale the image nicely */
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        height: 100vh;
        opacity: 0.5;}
        #title {
            text-align: center;
            font-size: 1.4em;
            font-weight: 300;
        }

        input {
            width: 20px;
        }
   </style>

</head>
<body>
 <div class="headerq">
 <div id="title" style="align: center"> JOB  SHEET </div>
<div id="line"></div>
<table style="border: 0px" width="700px">
<tr>
    <td width="400px">
       <p> {{ $client_name[0]->client_name ?? $sales[0]->customer_name }} </p>
       <p> {{ $client_name[0]->address }} </p>
       <p> {{ $client_name[0]->place }} </p>
       <p>  &nbsp; </p>
       <p>  &nbsp; </p>
       <p>  &nbsp; </p>
       <p>  &nbsp; </p>
    </td>
    <td>
    <p><strong>DOC REFERENCE </strong> </p>
    <p> Account No : </p>
    <p>Order Ref # : </p>    
    <p>Receive Date: </p>
    <p>Due Date: </p>   
     <br/>
    <p> Technician <br/>
    <label> <input type="checkbox" name=""> In Progress<label> 
    </p>
    </td>
    <td style="text-align: right">
    <p> <strong> JC{{ $sales[0]->job_card_reference }} </strong> </p> 
    <p>
        @if ($sales[0]->account_no < 10)
        {{ $sales[0]->account_prefix }}{{ '00'.$sales[0]->account_no }}
        @elseif ($sales[0]->account_no < 100)
        {{ $sales[0]->account_prefix }}{{ '0'.$sales[0]->account_no }}
        @else 
        {{ $sales[0]->account_prefix }}{{ $sales[0]->account_no }}
        @endif
    </p>    
    <p> {{ $sales[0]->reference }}</p>    
    <p> {{ $sales[0]->created_date }} </p> 
    <p> {{ $sales[0]->created_date }} </p>       
    <!-- <p> {{ $sales[0]->valid_estimate_date }} </p> -->
    <br/>
    <p> ________________  </p>
    <p> <label style="display: inline;"><input type="checkbox"/> Completed </label> </p>
  </td>
</tr>
</table>
 <div id="line"></div>
<span id="rpt"></span>
    </div>

             <table id="vehicle-reg" class="table table-striped table-bordered">
                      <thead >
                            <tr style="text-align: center">
                                <th>Vehicle Reg #</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Chassis No.</th>
                                <th>Milleage </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> {{ $sales[0]->vehicle_reg }} </td>
                                <td> {{ $sales[0]->emake }} </td>
                                <td> {{ $sales[0]->emodel }} </td>
                                <td> {{ $sales[0]->chassis }} </td>
                                <td> {{ $sales[0]->milleage }} </td>
                            </tr>
                        </tbody>
             </table>
       <br/>
        
       <div class="notes">
       <img src="{{ public_path().'/assets/images/vehicle.jpg' }}" alt="User profile" style="height: 120px; width: 290px;" class="center img-thumbnail img-responsive profile-pic">    

         <textarea rows="12" cols="30" style="width: 700px; height: 100px;"> Notes </textarea>  
        </div>
    <br/><br/>
       <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Labour  </th>
                                <th>Tech </th>
                                <th> Qty </th>
                                <th>Done </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($labours as $key => $l)
                            <tr>
                                <td width="460px"> {{ $l->labour_name }} </td>
                                <td></td>
                                <td></td>
                                <td><input type="checkbox" name="" class="form-control"> </td>   
                            </tr>
                          @endforeach  
                    </tbody>
            </table>
            <br/><br/>
    <div class="container table-responsive">
               <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Parts</th>
                                <th>Part No</th>
                                <th>Qty </th>
                                <th>Done</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td width="460px"> {{ $product->item_name }} </td>
                                <td>{{ '' }}</td>
                                <td>{{ $product->qty}}</td>
                                <td><input type="checkbox" name="" class="form-control"> </td>   
                            </tr>
                          @endforeach  
                    </tbody>
                    </table>
                    <br/>
                    
          <br/><br/>
                        
        </div> 
    <div>
</div>
</div>
</body>
</html>
