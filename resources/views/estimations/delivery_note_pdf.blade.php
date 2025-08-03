<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Delivery Note') }}</title>
   <style>
      tr th {
           background: #eee;
           padding-top: 10px;
           font-size: 13px;
      }
      h5 {
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

        #total_estimate {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 35%;
        }
        #total_estimate td, #sales_report th {
        border: 1px solid #ddd;
        padding: 8px 0 8px 8px;
        }

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 4px 0 3px 15px;
        font-size: 0.9em;
        }

        /* #sales_report tr:nth-child(even){background-color: #f2f2f2;} */

        #sales_report tr:hover {background-color: #ddd;}

        
        .page-break  {
            page-break-before: always;
            margin-top: 155px;
        }

        thead {
            display: table-row-group;  // remove thead on break break
        }

       #sales_report tr:nth-child(6n+1) {
        /* page-break-after: always; */
        }
       
        #sales_report th {
        padding-top: 6px;
        padding-bottom: 6px;
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
            margin-top: 3px;
        }
        #rpt h4 {
            text-align: right;
        }
          div .logo {
          /* /* display: block; */
          margin-left: 0;
          margin-top: 0;
          /* width: 50%;  */
          text-align: left;
          /* padding: 0 0 20px 0; */
       }
     .business_name {
          text-align: left;
          padding-left: 14px;
          font-size: 22px;
          font-weight: 300;
     }
ul {
  list-style-type: none;
  margin: 0;
  padding: 10;
}

/* main {
  The image used
        background-image: url("assets/images/logo_nduvini.jpeg");
        background-position: center;
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        background-size: 500px 500px;

} */


:root {
--watermark-opacity: 0.22;
}
.watermark {
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
width: 100%;
height: 100%;
background-image: url('assets/images/logo_nduvini.jpeg');
background-position: center;
background-repeat: no-repeat;
background-size: 400px;
opacity: var(--watermark-opacity);
z-index: -1;
pointer-events: none;
}

    @page {
        margin: 140px 25px 100px 25px;
        /* padding: 200px 0; */
        /* padding-bottom: 20px; */
       }
      
        header {
            position: fixed;
            top: -136px;
            width: 100%;
            height: 50px;
            font-size: 20px !important;
            /* background-color: #000; */
            /* color: white; */
            /* text-align: center; */
            /* line-height: 35px; */
        }
        footer #footer_ {
            background-color: red;
            color: #fff;
        }
        footer {
            position: fixed; 
            bottom: 65px; 
            left: 0px; 
            right: 0px;
            height: 100px; 
            font-size: 20px !important;
            width: 100%;
            /* text-align: center; */
            /* line-height: 35px; */
        }
        footer .page-number {
            /* text-align: center; */
        }
        .page-number:before {
            content: "Page  " counter(page);
        }
        p {
            line-height: 4px;
        }
        h4, h2 {
            color: blue;
        }
        #tbl_sign tr td {
            padding-top: 10px;
        }

       .branch {
       font-size: 0.8em;
       padding: 3px 0 2px 26px;
      }
       .subject {
          font-size: 1em;
         padding: 6px auto;
       }
.content {
  font-size: 0.9em;
}
.accept {
   padding: 4px;
   font-size: 1.1em;
}
   </style>

</head>
<body>
<header>
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 760px;" class="center img-thumbnail img-responsive profile-pic">    

<div id="line1"></div>
<div class="branch"> 
@if(Auth::user()->branch_id == 1)
    HEAD OFFICE :  Dar es Salaam 
@elseif (Auth::user()->branch_id == 2)
   BRANCH : TABORA 
@elseif (Auth::user()->branch_id ==3)
   BRANCH : DODOMA 
@else
@endif
 </div>
<div id="line"></div>
<table style="border: 0px" width="740px">
<tr>
    <td width="500px">
       <h4> Deliver To: </h4>
       <p> {{ $client_name[0]->client_name }} </p>
       <p> {{ $client_name[0]->address }} </p>
       <p> {{ $client_name[0]->place }} </p>
       
    </td>
    <td>
    <h4>DELIVERY NOTE <h4>
    <p> Delivery # :   <span style="float:right">
 @if ($sales[0]->job_card_ID < 10)
        {{ '00000'.$sales[0]->job_card_ID }}
        @elseif ($sales[0]->job_card_ID < 100)
        {{ '0000'.$sales[0]->job_card_ID }}
        @elseif ($sales[0]->job_card_ID < 1000)
        {{ '000'.$sales[0]->job_card_ID }}
        @elseif ($sales[0]->job_card_ID < 10000)
        {{ '00'.$sales[0]->job_card_ID }}
        @elseif ($sales[0]->job_card_ID < 100000)
        {{ '0'.$sales[0]->job_card_ID }}
        @else 
        {{ $sales[0]->job_card_ID ?? $sales[0]->invoice_number }}
        @endif
 
 </span>
    <p> Delivery Date : <span style="float:right"> {{ \Carbon\Carbon::parse($sales[0]->delivery_date)->format('Y-m-d') ?? $sales[0]->created_date }} </span> </p> 
    <p> P.O # :  <span style="float:right"> {{ $sales[0]->reference }} </span></p>    
    </td>
</t>
</table>
</header>
<br/><br/></br/><br/><br/>
<span id="rpt"><h4> </h4></span>
     <div id="line"></div>
<br/>
<footer>
<table style="border: 0px; padding: 0; margin: 0;" id="tbl_sign">
<tr>
    <td>Office Representative  : ......................................................................... </td>
    <td> Position : ...........................</td>
    <td> Mobile  : ......................  </td>
    </tr>
   <tr>
    <tr>
    <td colspan="2"><div class="accept"> <i> I am satisfied with the repairs of this vehicle as listed above. </i></div> </td>
    </tr>
<tr>
    <td> Signature:    ........................................... </td>
    <td> Date  :  .................................</td>
    </tr>
<tr>
<td> Workshop Representative :  <strong>{{ strtoupper(Auth::user()->fname) }} {{ strtoupper(Auth::user()->lname) }}</strong> </td>
<td> Position :  <strong>{{ $user_role->name }}</strong></td>
<td>Signature : .............................</td>
</tr>
</table> <br/>
<!-- <div class="footer" style="background: #ff0000; color: #fff"> -->
<!-- <div id="line"></div> -->
<table style="border: 0px; padding: 0; background-color: red; color: #fff;  margin: 0; width: 750px;">
   <tr>
   <td>
    <ul> 
        <li> HEAD OFFICE :  Temeke District - Chang'ombe </li>
        <li> P.o Box 45610 DSM </li> 
        <li> Tel: +255 22 2860279 |
         Fax +255 22 2860276 </li>
        <li> Phone: +255 754 275223 <li>
        <li> Email: nduvini@hotmail.com <li> 
       </ul>
  </td>
    <td>
       <ul> 
        <li> Branch Office : Singida Road- Kizota </li>
        <li> P.o Box 530 DODOMA </li>
        <li> Tel: +255 26 2340153 </li>
        <li> Phone: +255 753 915234 <li>
        <li> Email: dodoma@nduviniautoworks.co.tz <li> 
       </ul>
    </td>
    <td>
    <ul> 
        <li> Branch Office :  Tabora </li>
        <li> P.o Box 1057 Tabora </li>
        <li> Tel: +255 22 2860279 </li>
        <li> Phone: +255 754 275223 <li>
        <li> Email: nduvini@hotmail.com <li> 
       </ul>  
    </td>
    
</tr>
</table> 
<!-- </div> -->
</div>
</footer>

<main>
<div class="watermark"></div>

             <table id="sales_report" class="table table-striped table-bordered">
                      <thead>
                            <tr>
                                <th>Vehicle Reg. </th>
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
    <div class="container table-responsive">
<br/>
<div class="subject"><strong>RE: COMPLETION OF VEHICLE REPAIRS </strong></div>

<span class="content">I would like to inform you that the repairs of the aforementioned vehicle, which was taken to the workshop <br/> <strong>NDUVINI AUTO WORKS LTD </strong> on 
{{ \Carbon\Carbon::parse($sales[0]->created_date)->format('d M, Y') }}
 have been completed. </span> 

<h4><i><strong>The repairs carried out include the following: </strong> </i></h4>

               <table id="sales_report" class="table table-striped table-bordered">
                        <thead class="part-header">
                            <tr>
                               <th># </th>
                                <th>Particular</th>
                                <th>Qty </th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        @foreach($sales as $key => $product)
                            <tr>
                               <td> {{ $key+1 }} </td>
                                <td width="600px"> {{ $product->item_name }}  </td>
                                <td>{{ $product->qty }}
                              @if($product->qty > 1)
                                {{ strtoupper($product->purchase_unit) }}S
                                @else
                                 {{ strtoupper($product->purchase_unit) }}
                               @endif
                            </td>
                            </tr>
                             <!-- Insert line break after every 30 rows -->
                            
                             @if (($key + 1) % 18 == 0 && $key + 1 < count($sales))
                                <div class="page-break"></div>
                                
                            @endif
                          @endforeach  
                    </tbody>
                    </table>
                    <br/>
                    
        </div> 
    <div>

<main>

</body>
</html>
