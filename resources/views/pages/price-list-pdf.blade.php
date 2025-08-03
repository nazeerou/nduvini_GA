<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Price Sheet') }}</title>

   <style>
        h4 {
          /* text-align: center; */
      }
      tr th {
           background: #eee;
           padding: 10px;
           font-size: 10px;
           text-transform: uppercase;
      }
      h5 {
          text-align: right;
          font-size: 1em;
      }
      table {
          font-size: 12px;
      }
      #datatable {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #datatable td, #datatable th {
        border: 1px solid #ddd;
        padding: 6px 0 4px 13px;
        text-align: left;
        }

        #sales_report td, #sales_report th {
        border: 1px solid #ddd;
        padding: 4px 0 0px 10px;
        font-size: 0.9em;

        }

        header {
            position: relative;
            top: -44px;
            width: 100%;
            height: 55px;
            font-size: 20px !important;
            /* background-color: #000; */
            /* color: white; */
            /* text-align: center; */
            /* line-height: 35px; */
        }
        #line {
            border: 1px solid #ddd;
        }
   </style>

</head>
<body>
<header>
 <img src="{{ public_path().'/assets/images/nduvini_header.png' }}" alt="User profile" style="height: 130px; width: 710px;" class="center img-thumbnail img-responsive profile-pic">    
<div id="line"></div>
</header>
<div class="row m-t-30">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
            <div class="panel-body">
            <div class="row">
<br/>                                                              
 <h4 align="center"> @if (!$clients) GENERAL - PRICE LIST @else {{ $clients->client_name }} - PRICE LIST @endif

                    <table id="datatable" class="table table-striped table-bordered">
                    <thead>
                            <tr>
                                <th> # </th>
                                <th>Part Name</th>
                                <th> Description </th>
                                <th> Unit Price </th>
                            </tr>
                        </thead>

                        <tbody id="display_result">
                        @foreach($products as $key => $product)
                            <tr>
                                <td width="30"> {{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }}  - 
                                 {{ $product->model }} | {{ $product->part_number }} </td>
                                <!-- <td>{{ number_format($product->purchasing_price, 2) }}</td> -->
                                <td>{{ number_format($product->sale_price, 2) }}</td>
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>
</body>
</html>
