<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Current Stock ') }}</title>

   <style>
        h4 {
          text-align: center;
      }
      tr th {
           background: #eee;
           padding: 10px;
           font-size: 10px;
           text-transform: uppercase;
      }
      h5 {
          text-align: left;
          font-size: 0.8em;
      }
      #datatable {
          font-size: 12px;
      }
      #datatable {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }

        #datatable td, #datatable th {
        border: 1px solid #ddd;
        padding: 4px 0 4px 10px;
        }

        #datatable th {
        border: 1px solid #ddd;
        padding: 8px 0 8px 10px;
        font-size: 0.9em;
        text-align: left;
        }
        header {
            position: relative;
            top: -38px;
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
<div class="row"><br/><br/>
            <h4> CURRENT STOCK </h4>
             <h5>BRANCH : {{ $branches[0]->branch_name }} </h5>
             <h5 style="text-align: left"> Date : <?php echo date('d - M, Y'); ?> </h5>
            <div class="panel-body">
                    <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Part Name</th>
                                <TH>Description </th>
                                <th>QTY </th>
                                <th> Unit </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($stocks as $key => $product)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td> {{ $product->item_name }} </td>
                                <td> {{ $product->title }} 
                                | {{ $product->model }} | {{ $product->part_number }} </td>
                                <td>{{ $product->quantity}}</td>
                                <td>{{ $product->purchase_unit}}</td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
            
                      </div>
                  </div>
            </div>
      </div>
</div>
<!-- END  -->
</body>
</html>