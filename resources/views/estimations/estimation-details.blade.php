@extends('layouts.app_header')
@section('content')

<style>
  #input-detail {
      border: 1px solid red;
  }

  /* ===== Edit Proforma Modal Styling ===== */
  .proforma-modal .modal-header {
      background: #f5f5f5;
      border-bottom: 2px solid #e0e0e0;
      border-radius: 6px 6px 0 0;
  }
  .proforma-modal .modal-title {
      font-weight: 600;
      color: #2c3e50;
  }
  .proforma-modal .modal-body {
      padding: 22px 28px;
      background: #fafafa;
  }
  .proforma-modal .form-group label {
      font-weight: 600;
      color: #333;
      margin-bottom: 6px;
      font-size: 13px;
  }
  .proforma-modal .form-control {
      border-radius: 4px;
      border: 1px solid #d0d0d0;
      transition: border-color .2s, box-shadow .2s;
  }
  .proforma-modal .form-control:focus {
      border-color: #5cb85c;
      box-shadow: 0 0 6px rgba(92, 184, 92, 0.25);
  }
  .proforma-modal .form-control[readonly] {
      background-color: #f7f7f7;
      cursor: not-allowed;
  }
  .proforma-modal .radio-inline {
      margin-right: 22px;
      margin-left: 0;
      font-weight: 500;
  }
  .proforma-modal .radio-info input[type="radio"]:checked + label::after {
      background-color: #5bc0de;
  }
  .proforma-modal .vat-wrapper {
      text-align: right;
      border-left: 1px dashed #ccc;
      padding-left: 15px;
      min-height: 55px;
  }
  .proforma-modal .temesa-wrapper {
      text-align: left;
      padding-right: 15px;
      min-height: 55px;
  }
  .proforma-modal .radio-label-title {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #2c3e50;
      text-align: left !important;
  }
  .proforma-modal .lock-badge {
      font-size: 11px;
      padding: 3px 9px;
      margin-left: 8px;
      border-radius: 10px;
      vertical-align: middle;
  }
  .proforma-modal .proforma-warning {
      border: 2px solid #f0ad4e !important;
      background-color: #fff8e8 !important;
  }
  .proforma-modal .proforma-locked-hint {
      font-size: 11px;
      color: #a94442;
      margin-top: 4px;
      display: block;
  }
  .proforma-modal .proforma-unlock-box {
      background: #fff3cd;
      border-left: 4px solid #f0ad4e;
      padding: 10px 15px;
      border-radius: 4px;
      margin-top: 8px;
      display: none;
  }
  .proforma-modal .proforma-unlock-box textarea {
      border: 1px solid #f0ad4e;
  }
  .proforma-modal .modal-footer {
      background: #f5f5f5;
      border-top: 1px solid #e0e0e0;
      border-radius: 0 0 6px 6px;
  }
  .proforma-modal .section-divider {
      border-top: 1px dashed #d0d0d0;
      margin: 18px 0;
  }
  @keyframes proformaFlash {
      0% { background-color: #fff8e8; }
      50% { background-color: #ffe4b5; }
      100% { background-color: #fff8e8; }
  }
  .proforma-modal .proforma-warning {
      animation: proformaFlash 1s ease-in-out;
  }

  /* Date picker above modal */
  .datepicker,
  .datepicker-dropdown {
      z-index: 1151 !important;
  }
  .proforma-modal #created_date {
      background-color: #fff;
      cursor: pointer;
  }

  .modal .modal-dialog .modal-content {
    -moz-box-shadow: none;
    -webkit-box-shadow: none;
    border-color: #DDDDDD;
    border-radius: 2px;
    box-shadow: none;
    padding: 55px !important;
}

    #editProfomaModal .modal-dialog {
        width: 90%;
        max-width: 950px;
        margin: 30px auto;
    }

    #editProfomaModal .modal-content {
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, .12);
    }

    #editProfomaModal .modal-header {
        padding: 20px 25px;
        background: #f7f8fa;
        border-bottom: 1px solid #e8eaed;
    }

    #editProfomaModal .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #303846;
        margin: 0;
    }

    #editProfomaModal .modal-title i {
        margin-right: 9px;
        color: #64748b;
    }

    #editProfomaModal .modal-body {
        padding: 0px 25px;
        background: #fff;
    }

    #editProfomaModal .form-group {
        margin-bottom: 19px;
    }

    #editProfomaModal .control-label {
        display: block;
        margin-bottom: 7px;
        color: #46505e;
        font-size: 12px;
        font-weight: 600;
    }

    #editProfomaModal .form-control {
        height: 40px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
        box-shadow: none;
        color: #354052;
        font-size: 13px;
        transition: border-color .2s ease;
    }

    #editProfomaModal textarea.form-control {
        height: auto;
        min-height: 75px;
        resize: vertical;
    }

    #editProfomaModal .form-control:focus {
        border-color: #8b99a9;
        box-shadow: 0 0 0 2px rgba(100, 116, 139, .08);
    }

    #editProfomaModal .form-control[readonly] {
        background: #f5f6f8;
        cursor: not-allowed;
    }

    #editProfomaModal .input-group-addon {
        background: #f5f6f8;
        border-color: #dce1e7;
        color: #64748b;
    }

    #editProfomaModal .input-group-btn .btn {
        height: 40px;
        border-radius: 0 5px 5px 0;
    }

    #editProfomaModal .section-heading {
        display: flex;
        align-items: center;
        gap: 9px;
        margin: 0px 0 20px;
        padding-bottom: 11px;
        border-bottom: 1px solid #edf0f3;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    #editProfomaModal .section-heading i {
        color: #8491a2;
        font-size: 15px;
    }

    #editProfomaModal .section-divider {
        height: 1px;
        background: #edf0f3;
        margin: 8px 0 22px;
    }

    #editProfomaModal .lock-badge {
        margin-left: 7px;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        vertical-align: middle;
    }

    #editProfomaModal .proforma-locked-hint {
        display: block;
        margin-top: 7px;
        color: #8792a2;
        font-size: 11px;
        line-height: 17px;
    }

    #editProfomaModal .proforma-locked-hint i {
        margin-right: 4px;
    }

    #editProfomaModal .proforma-unlock-box {
        display: none;
        padding: 16px;
        margin: 0 0 20px;
        background: #fafafa;
        border: 1px solid #e3e6eb;
        border-left: 3px solid #b7791f;
        border-radius: 5px;
    }

    #editProfomaModal .proforma-unlock-box .control-label {
        color: #9a6419;
    }

    #editProfomaModal .radio-label-title {
        display: block;
        margin-bottom: 12px;
        color: #46505e;
        font-size: 12px;
        font-weight: 700;
    }

    #editProfomaModal .temesa-wrapper,
    #editProfomaModal .vat-wrapper {
        padding: 17px 20px;
        margin-bottom: 12px;
        background: #fafbfc;
        border: 1px solid #e8ebef;
        border-radius: 6px;
        min-height: 90px;
    }

    #editProfomaModal .radio {
        margin-top: 5px;
        margin-bottom: 0;
    }

    #editProfomaModal .modal-footer {
        padding: 16px 25px;
        background: #f8f9fa;
        border-top: 1px solid #e8eaed;
    }

    #editProfomaModal .btn-rounded {
        border-radius: 5px;
        padding: 9px 19px;
        font-size: 12px;
        font-weight: 600;
    }

    #editProfomaModal .btn-success {
        background: #526b60;
        border-color: #526b60;
    }

    #editProfomaModal .btn-success:hover {
        background: #40564c;
        border-color: #40564c;
    }

    #editProfomaModal .select2-container {
        width: 100% !important;
    }

    #editProfomaModal .select2-container .select2-selection--single {
        height: 40px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
    }

    #editProfomaModal .select2-container .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        font-size: 13px;
        color: #354052;
    }

    #editProfomaModal .select2-container .select2-selection__arrow {
        height: 38px;
    }

    @media (max-width: 767px) {
        #editProfomaModal .modal-dialog {
            width: auto;
            margin: 10px;
        }

        #editProfomaModal .modal-header,
        #editProfomaModal .modal-body,
        #editProfomaModal .modal-footer {
            padding: 16px;
        }

        #editProfomaModal .temesa-wrapper,
        #editProfomaModal .vat-wrapper {
            margin-bottom: 12px;
        }

        #editProfomaModal .modal-footer .btn {
            margin-bottom: 5px;
        }
    }
</style>

<div class="row m-t-5">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="btn btn-sm btn-" href="{{ url('estimations-history') }}"> <i class="ti-arrow-left"></i> &nbsp; GO BACK</a> <br/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
           
    <div class="panel-heading">
    <div class="panel-body">
                 <h3> Estimation Cost : </br></h3>
                 <h4 class="panel-title1"> 
               @if($client_name[0]->client != NULL)
                 CLIENT NAME : <strong>  {{ $client_name[0]->client }}</strong><br/>
                @else 
                    {{ $sales[0]->customer_name }}                
                @endif    
                <br/>
                VEHICLE REG# : <strong>  {{ $sales[0]->vehicle_reg }} </strong>
                </h4>
                <!-- <br/> -->
                <div style="float: right">
                    <a href="{{ url('estimations/details/pdf/'.$id) }}" target="_blank" class="btn btn-sm btn-pink"> <i class="fa fa-file-pdf-o"> </i> &nbsp; Create Profoma  </a> 
                    <button type="button" class="btn btn-success btn-sm" data-id="{{ $id }}" id="editProfoma" data-name="edit-proforma" data-toggle="modal" data-target="#editProfomaModal">Edit Proforma</button>
                     <a href="{{ url('mails/proforma-invoice/'.$client_name[0]->client.'/'.$id) }}" class="btn btn-sm btn-primary"> <i class="fa fa-envelope"> </i> &nbsp; Send </a>
                </div> <br/>
            </div>
<div class="container table-responsive">
<div class="row">
        <div class="col-md-10">
        <div id="btn" style="float: right"> 
            <button type="button" class="btn btn-success btn-sm addNew" data-name="Add New " data-toggle="modal"> + Add Part </button>
        </div>
</div>
</div>
<div class="row">
        <div class="col-md-10">
               <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Parts</th>
                                <th>Qty </th>
                                <th>Unit</th>
                                <th>D (%) </th>
                                <th>Sub Total </th>
                                <th>Action </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $key => $product)
                            <tr>
                                <td width="400px"> {{ $product->item_name }} </td>
                                <td>{{ $product->qty}}</td>
                                <td> {{number_format( $product->selling_price, 2) }} </td>
                                <td> {{ $product->discount ? : ''  }} {{ $product->discount ? '%' : ''  }}    </td>
                                <td> {{number_format($product->total_sales, 2) }} </td>   
                                <td>  
                                <button type="button" name="submit" data-id="{{ $product->id }}" class="btn btn-sm  btn-info waves-effect waves-light edit-part_details" data-target="#myModal"> <i class="fa fa-edit"></i> </button>
                                <a class="btn btn-sm btn-danger" href="{{ url('/estimations/delete-estimations/'.$product->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i>  </a>
                             </td>
                            </tr>
                          @endforeach  
                    </tbody>
                    </table>
</div>
</div>
<br/>
<div class="row">
        <div class="col-md-10">
        <div id="btn" style="float: right"> 
            <button type="button" class="btn btn-success btn-sm addLabour" data-name="Add More" data-toggle="modal"> + Add Labour </button>
        </div>
</div>
</div>
    <div class="row">
        <div class="col-md-10">
                    <table id="sales_report" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                            <th>Labour </th>
                                <th>Rate/Hr </th>
                                <th>Unit </th>
                                <th>D </th>
                                <th>Sub Total </th>
                                <th> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($labours as $key => $l)
                            <tr>
                                <td width="660px"> {{ $l->labour_name }} </td>
                                <td> {{ $l->qty }} </td>
                                <td> {{ $l->charge }} </td>
                                <td> </td>
                                <td> {{number_format(($l->total_amount), 2) }} </td> 
                                <td> 
                                <a class="btn btn-sm btn-danger" href="{{ url('estimations/labours/delete/'.$l->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i>  </a>   
                               </td>     
                            </tr>
                          @endforeach  
                    </tbody>
            </table>
            </div>
    </div>
    <br/>
<div class="row">
<div class="col-md-6"></div>
<div class="col-md-4">
                        <table id="total_estimate"  class="table table table-bordered" style="color: black">
                           <thead>
                           @if($total_discounts != NULL)
                               <tr>
                                 <td> <strong> Net Discount : </strong> </td>
                                    <td> {{ number_format($total_discounts, 2) }} </td>
                                </tr>
                                @else  {{ '' }} @endif 
                                 <tr>
                                 <tr>
                                 <td> Parts : </td>
                                    <td> {{ number_format($total_sales, 2) }} </td>
                                </tr>
                                <tr>
                                 <td> Labour : </td>
                                    <td> {{ number_format($total_labours, 2) }} </td>
                                </tr>
                                 @if (!empty($temesa_fee) && $temesa_fee > 0)
                                    <tr>
                                        <td>TEMESA Fee (8%):</td>
                                        <td>{{ number_format($temesa_fee, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                 <td>
                                     <strong> Sub Total : </strong>
                                    </td>
                                    <td width="100px"> {{ number_format(($total_sales + $total_labours+ $temesa_fee), 2) }} </td>
                                </tr>
                                <tr>
                                 <td>
                                      VAT Amount (18%)  :
                                    </td><td>  
                                          {{ number_format($vat_charges, 2) }}
                                      </td>
                                </tr>
                                <tr style="background: #eee">
                                 <td>
                                     <strong> Total Estimate  </strong>
                                      &nbsp; : 
                                    </td><td>  
                                          {{ number_format(($grand_total_amount), 2) }}
                                      </td>  
                            </tr>
                     </thead>
              </table>
              
        <br/><br/><br/>
</div>
                   
                   
            </div>
      </div>
</div>


  <!-- Add Spare Parts Modal -->
      <div class="modal modal-xl fade bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true"  id="myModal" role="dialog">
            <div class="modal-dialog modal-lg">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal">&times;</button>
                     <h4 class="modal-title">Add Spare Parts </h4>
                  </div>
                  <div class="modal-body">
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Client    * </label>
                                <select name="client[]" class="form-control select2" id="client" >
                                <option value=""> --- General --- </option>
                                @foreach($clients as $client)
                                   <option value="{{ $client->id }}">  {{ $client->client_name }}  </option>
                                 @endforeach
                                </select>
                             </div> 
                          </div>
                   <div class="col-md-7"></div>
               </div>
                  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
                <div class="row m-t-10">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Part Name   * </label>
                                <select name="product_id[]" class="form-control select2" id="product_id">
                                <option value=""> --- Select Parts --- </option>
                                 @foreach($products as $product)
                                   <option value="{{ $product->id }}">  {{ $product->item_name }}  -{{ $product->title }} | {{ $product->model }}  :  ({{ $product->quantity }}) </option>
                                 @endforeach
                                </select>
                             </div>
                         </div>
                  </div>

     <div id="client_details" style="padding: 0px; border: 0px solid #ddd;">
         <div class="row m-t-20">
        <div class="col-md-12">
        <form id="formPost" method="POST" action="{{ url('/estimations/add-more-parts') }}" enctype="multipart/form-data"> 
                  @csrf
         <input type="hidden" name="reference" value="{{ $id }}" class="form-control">
         <input type="hidden" name="client_id"  value="{{ $client_name[0]->client_name }}" class="form-control">
        <section id='Table'>
        <table class="table table-striped table-bordered" style="background: #fff;" id="myTable1">
            <thead>
                <tr>
                    <th>#</th>
                    <th width="200px">Part Name</th>
                    <th width="100px"> D (%)</th>
                    <th width="150px"> Price</th>
                    <th width="100">Qty</th>
                    <th width="150px"> Sub Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows'></tbody>
           </table>
          </section>
        </div>
      </div>  
    </div>
</div>
    <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
        </div>
</div>
</form>
</div>
</div>
</div>    
</div>
</div>
</div>

<!-- Edit Part Modal -->
<div class="modal fade modal-xl bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="details_modal">
      <form id="formUpdate" method="post" action="{{ url('../estimations/update-part-details') }}">
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Part Details </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Part Name    </label>
                                <input type="text" name="product_name" id="product" class="form-control" readonly>
                                <input type="hidden" name="id"   id="id" class="form-control" placeholder="Product Name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Make  </label>
                                <input type="text" name="make" id="make" class="form-control" readonly placeholder="">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Model </label>
                                <input type="text" name="model" id="model" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row m-b-10">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Selling Price   </label>
                                <input type="number" step="0.01" min="0" name="selling_price" id="selling_price" class="form-control" placeholder="Amount" required>
                             </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Qty * </label>
                                <input type="number" step="1" min="1" name="quantity" id="qty" class="form-control" placeholder="Qty" required>
                             </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Discount(%) </label>
                                <input type="number" step="0.01" min="0" max="100" name="discount" id="discount" class="form-control" placeholder="Discount %">
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-rounded btn-success waves-effect waves-light"> Update </button>
                    </div>
                 </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->
</div>

<!-- Add Labour Modal -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="labour_modal">
<form id="formUpdate" method="post" action="{{ url('../estimations/add-new-labour-details') }}">
            @csrf
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title" id="myLargeModalLabel">Add or Change Labour Details </h4>
        </div>
        <div class="modal-body">
        <div class="row m-t-0">
     <div class="col-md-12">
  <div id="details" style="padding: 10px; border: 1px solid #ddd;">
  <input type="hidden" name="reference" value="{{ $id }}" class="form-control">

    <div class="row m-t-10">
        <div class="col-md-3">
            <div class="form-group">
                <label for="inputEmail3" class="control-label"> Labour   * </label>
                    <select name="labour[]" class="form-control select2" id="labour">
                    <option value=""> --- Select Labour --- </option>
                        @foreach($labour_settings as $l)
                        <option value="{{ $l->id }}"> {{ strtoupper($l->labour) }} </option>
                        @endforeach
                    </select>
                    </div> 
        </div>
        <div class="col-md-9">
        <section id='Table' class='container'>
        <table class="table table table-bordered" style="background: #fff;" id="myTable2">
            <thead>
                <tr class="target_3">
                    <th width="200px">Labour </th>
                    <th width="100px">Unit </th>
                    <th width="20">Rate/Hr</th>
                    <th width="50">Sub Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='dataRows2'></tbody>
              </table>
            </section>
            </div>
          </div>  
          <div class="row mt-20">
          <table class="table" id="myTableOther">
              <tr class="target_2">
                    <td>
                        <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> General (Charges)  </label>
                            <input type="text" name="labour_name[]"  class="form-control" placeholder="Name of general charges ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Amount </label>
                            <input type="text" name="unit_charge[]"  class="form-control charge" placeholder="Enter Amount Eg. 50000 ">
                            </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inputEmail3" class="control-label"> Percent (%) </label>
                            <input type="text" name="labour_qty[]"  class="form-control qty" placeholder="Enter  Eg. 20"> 
                            <input type="hidden" name="total_amount[]"  class="form-control subtotal"> 
                            </div> 
                    </div> 
                      </td>
                    </tr>
             </table>
           </div>
        </div>
    </div>
</div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-rounded w-lg waves-effect waves-light m-b-20 m-t-10" id="save_btn1">Save  </button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</form>
</div><!-- /.modal -->
</div>

<!-- ============ PROFESSIONAL EDIT PROFORMA MODAL ============ -->

<div class="modal fade proforma-modal" id="editProfomaModal"
     tabindex="-1" role="dialog"
     aria-labelledby="editProfomaModalLabel" aria-hidden="true">

    <form id="editProfomaForm" method="post"
          action="{{ url('estimations/update-profoma-details') }}">

        @csrf

        <input type="hidden" name="id" id="edit_id">
        <input type="hidden" name="updated_by" value="{{ Auth::id() }}">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h4 class="modal-title" id="editProfomaModalLabel">
                        <i class="fa fa-pencil-square-o"></i>
                        Edit Proforma Details
                    </h4>
                    <small class="text-muted">
                        Update client, vehicle and proforma information
                    </small>
                </div>

                <!-- BODY -->
                <div class="modal-body">

                    <!-- CLIENT INFORMATION -->
                    <div class="section-heading">
                        <i class="fa fa-user"></i>
                        Client Information
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label">Client</label>

                                <select name="client_name" id="edit_client"
                                        class="form-control select2">
                                    <option value="">--- General ---</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- VEHICLE INFORMATION -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Vehicle Reg #</label>
                                <input type="text" name="vehicle_reg"
                                       id="vehicle_registration"
                                       class="form-control"
                                       placeholder="Enter vehicle registration">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Reference #</label>
                                <input type="text" name="reference"
                                       id="edit_proforma_reference"
                                       value="{{ $id }}"
                                       class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">
                                    Proforma Invoice #
                                    <span class="label label-default lock-badge"
                                          id="lock_badge">Locked</span>
                                </label>

                                <div class="input-group">
                                    <input type="text"
                                           name="proforma_invoice"
                                           id="proforma_invoice"
                                           class="form-control proforma-locked"
                                           readonly>

                                    <span class="input-group-btn">
                                        <button type="button"
                                                class="btn btn-warning"
                                                id="unlock_proforma"
                                                title="Unlock to edit (requires reason)">
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    </span>
                                </div>

                                <small class="proforma-locked-hint">
                                    <i class="fa fa-info-circle"></i>
                                    Proforma number is locked. Unlock only for
                                    critical corrections.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Make</label>
                                <input type="text" name="make" id="make_"
                                       class="form-control"
                                       placeholder="Enter vehicle make">
                            </div>
                        </div>
                    </div>

                    <!-- UNLOCK REASON -->
                    <div class="proforma-unlock-box" id="proforma_reason_box">
                        <label class="control-label">
                            <i class="fa fa-exclamation-triangle"></i>
                            Reason for changing Proforma Number
                            <span class="text-danger">*</span>
                        </label>

                        <textarea name="proforma_change_reason"
                                  id="proforma_change_reason"
                                  rows="2"
                                  class="form-control"
                                  placeholder="Explain why the proforma number must be changed..."></textarea>

                        <small class="text-muted">
                            This change will be logged with your user ID
                            (<strong>{{ Auth::user()->fname ?? '' }}
                                {{ Auth::user()->lname ?? '' }}</strong>).
                        </small>
                    </div>

                    <!-- ADDITIONAL VEHICLE DETAILS -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Model</label>
                                <input type="text" name="model" id="model_"
                                       class="form-control"
                                       placeholder="Enter vehicle model">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Chassis No.</label>
                                <input type="text" name="chassis" id="chassis"
                                       class="form-control"
                                       placeholder="Enter chassis number">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Mileage</label>
                                <input type="number" step="1" min="0"
                                       name="milleage" id="milleage"
                                       class="form-control"
                                       placeholder="Enter mileage">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Created Date</label>

                                <div class="input-group">
                                    <input type="text"
                                           class="form-control"
                                           id="created_date"
                                           name="created_date"
                                           autocomplete="off"
                                           placeholder="YYYY-MM-DD"
                                           data-date-format="yyyy-mm-dd">

                                    <span class="input-group-addon"
                                          id="created_date_icon">
                                        <i class="ti-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAX SETTINGS -->
                    <div class="section-heading">
                        <i class="fa fa-calculator"></i>
                        Tax & Fee Settings
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="temesa-wrapper">
                                <span class="radio-label-title">
                                    TEMESA Fee?
                                </span>

                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="temesa_yes"
                                           name="temesa_fee" value="0.08">
                                    <label for="temesa_yes">Yes</label>
                                </div>

                                <div class="radio radio-inline">
                                    <input type="radio" id="temesa_no"
                                           name="temesa_fee" value="0" checked>
                                    <label for="temesa_no">No</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="vat-wrapper">
                                <span class="radio-label-title">
                                    VAT (18%)?
                                </span>

                                <div class="radio radio-info radio-inline">
                                    <input type="radio" id="vat_with"
                                           name="vat_amount" value="0.18">
                                    <label for="vat_with">With VAT</label>
                                </div>

                                <div class="radio radio-inline">
                                    <input type="radio" id="vat_without"
                                           name="vat_amount" value="0" checked>
                                    <label for="vat_without">Without VAT</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-default btn-rounded"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success btn-rounded"
                            id="updateProformaBtn">
                        <i class="fa fa-save"></i> Update Proforma
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

    

<script>
 $(function() {
    $("#myTableOther").keyup(function(event) {
        var sub = 0;
        $("#myTableOther .target_2").each(function() {
            var qty = parseFloat($(this).find(".qty").val());
            var price = parseFloat($(this).find(".charge").val());
            var subtotal = parseFloat((qty/100) * price);
            $(this).find(".subtotal").val(subtotal);
            if(!isNaN(subtotal)) sub+=subtotal;
        });
        $(".subtotal").val(sub);
     });
 })

$(function() {
    $("#myTable1").keyup(function(event) {
        var sub = 0;
        $("#myTable1 .target").each(function() {
            var qty = parseFloat($(this).find(".qty").val());
            var discount = parseFloat($(this).find(".discount").val());
            var price = parseFloat($(this).find(".selling_price").val());
            var discount_value = (discount/100) * price;
            var total_price = price - discount_value;
            var subtotal = qty * total_price;
            $(this).find(".total_").val(subtotal);
            if(!isNaN(subtotal)) sub+=subtotal;
        });
     });
 })

 $("#myTable2").keyup(function(event) {
        var sub = 0;
        $("#myTable2 .target_3").each(function() {
            var qty = parseFloat($(this).find(".qty").val());
            var price = parseFloat($(this).find(".charge").val());
            var subtotal = (qty * price);
            $(this).find(".subtotal_other").val(subtotal);
            if(!isNaN(subtotal)) sub+=subtotal;
        });
     });

  $(document).ready(function () {

    // Variable to store original proforma number for detecting changes
    let originalProformaNumber = '';
    let proformaUnlocked = false;

    // Reset the lock state to "locked" every time the modal opens
    function resetProformaLock() {
        proformaUnlocked = false;
        $('#proforma_invoice')
            .prop('readonly', true)
            .removeClass('proforma-warning')
            .addClass('proforma-locked');
        $('#lock_badge')
            .removeClass('label-danger')
            .addClass('label-default')
            .text('Locked');
        $('#unlock_proforma')
            .prop('disabled', false)
            .removeClass('btn-danger')
            .addClass('btn-warning')
            .html('<i class="fa fa-lock"></i>');
        $('#proforma_reason_box').hide();
        $('#proforma_change_reason').val('');
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#myTable1").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });
      $("#myTable2").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });


      $('#myModal').modal({
        backdrop: 'static',
        keyboard: false,
        show: false,
    });
  
    $(document).on("click", ".addNew", function() {
        $('#myModal').modal('show');
    });
  
    $(document).on("click", ".addLabour", function() {
        $('#labour_modal').modal('show');
    });


    $('.edit-part_details').click(function() {
         var part_id = $(this).data('id');   
         $.ajax({
             url: `../../../estimations/edit-spareparts-details/${part_id}`,
             type: "GET",
             data: {part_id: part_id},
             success: function (response) {
                 $('#details_modal').modal('show');
                 $("#product").val(response[0].item_name);
                 $("#selling_price").val(response[0].selling_price);
                 $("#qty").val(response[0].qty);
                 $("#model").val(response[0].model);
                 $("#make").val(response[0].title);
                 $("#id").val(response[0].id);
                 $("#discount").val(response[0].discount);
             }
         });
    });


    // =============================================================
    // Date picker: init once the modal is fully shown.
    // This is safe (guarded) — if the datepicker plugin is not loaded,
    // it does nothing and the modal still works.
    // =============================================================
    $('#editProfomaModal').on('shown.bs.modal', function () {
        var $dateInput = $('#created_date');

        if (typeof $.fn.datepicker !== 'function') {
            return; // plugin not loaded; skip safely
        }

        if ($dateInput.data('datepicker')) {
            $dateInput.datepicker('destroy');
        }

        $dateInput.datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'auto bottom',
            container: 'body'
        });

        var existing = ($dateInput.val() || '').trim();
        if (existing) {
            try { $dateInput.datepicker('update', existing); } catch (e) { /* ignore */ }
        }
    });

    // Click the calendar icon to open the picker
    $('#created_date_icon').on('click', function () {
        if (typeof $.fn.datepicker === 'function') {
            $('#created_date').datepicker('show');
        }
    });

    // Open Edit Proforma modal — load data via AJAX
    $('#editProfoma').click(function() {
         var reference = $(this).data('id');   

         resetProformaLock();

         $.ajax({
             url: `../../../estimations/edit-profoma-details/${reference}`,
             type: "GET",
             data: {reference: reference},
             success: function (response) {
                 // Modal is already opening via data-toggle="modal" on the button.
                 // Just populate the fields.
                 $("#vehicle_registration").val(response[0].vehicle_reg);
                 $("#make_").val(response[0].make);
                 $("#chassis").val(response[0].chassis);
                 $("#model_").val(response[0].model);
                 $("#proforma_invoice").val(response[0].profoma_invoice);
                 $("#milleage").val(response[0].milleage);

                 // Trim any time component so datepicker accepts the value
                 var createdDate = (response[0].created_date || '').toString().trim();
                 if (createdDate.indexOf(' ') > -1) createdDate = createdDate.split(' ')[0];
                 if (createdDate.indexOf('T') > -1) createdDate = createdDate.split('T')[0];
                 $("#created_date").val(createdDate);

                 // Save the original proforma number
                 originalProformaNumber = (response[0].profoma_invoice || '').toString().trim();

                 // Default client
                 $("#edit_client").val(response[0].client_name).trigger('change');

                 // VAT radio
                 var vatValue = parseFloat(response[0].vat_amount);
                 if (!isNaN(vatValue) && vatValue > 0) {
                     $('#vat_with').prop('checked', true);
                 } else {
                     $('#vat_without').prop('checked', true);
                 }

                 // TEMESA radio
                 if (response[0].temesa_fee && parseFloat(response[0].temesa_fee) > 0) {
                     $('#temesa_yes').prop('checked', true);
                 } else {
                     $('#temesa_no').prop('checked', true);
                 }
             },
             error: function (xhr) {
                 console.error('edit-profoma-details failed:', xhr.status, xhr.responseText);
                 alert('Could not load proforma details. Check the console for errors.');
             }
         });
    });

    // Unlock proforma number for editing
    $('#unlock_proforma').on('click', function() {
        if (proformaUnlocked) return;

        swal({
            title: "Unlock Proforma Number?",
            text: "Editing the Proforma number should only be done for critical corrections. This change will be LOGGED with your user ID.",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Unlock",
            confirmButtonColor: "#f0ad4e",
            cancelButtonText: "Cancel",
            closeOnConfirm: true
        }, function(isConfirm) {
            if (isConfirm) {
                proformaUnlocked = true;
                $('#proforma_invoice')
                    .prop('readonly', false)
                    .removeClass('proforma-locked')
                    .addClass('proforma-warning')
                    .focus();
                $('#lock_badge')
                    .removeClass('label-default')
                    .addClass('label-danger')
                    .text('Unlocked');
                $('#unlock_proforma')
                    .prop('disabled', true)
                    .removeClass('btn-warning')
                    .addClass('btn-danger')
                    .html('<i class="fa fa-unlock"></i>');
                $('#proforma_reason_box').slideDown();
            }
        });
    });

    // Confirm before submitting the proforma update
    $('#editProfomaForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var currentProforma = ($('#proforma_invoice').val() || '').trim();
        var proformaChanged = (currentProforma !== originalProformaNumber);

        if (proformaChanged) {
            if (!$('#proforma_change_reason').val().trim()) {
                swal({
                    title: "Reason Required!",
                    text: "You must provide a reason before changing the Proforma number.",
                    type: "warning",
                    confirmButtonColor: "#f0ad4e"
                });
                return false;
            }
            swal({
                title: "Critical Change!",
                text: "You are about to change the Proforma Number from '" +
                      originalProformaNumber + "' to '" + currentProforma +
                      "'. This action will be LOGGED with your user ID. Are you sure?",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Update & Log",
                confirmButtonColor: "#d9534f",
                cancelButtonText: "Cancel",
                closeOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) form.submit();
            });
        } else {
            swal({
                title: "Update Proforma?",
                text: "Are you sure you want to update this Proforma?",
                type: "info",
                showCancelButton: true,
                confirmButtonText: "Yes, Update",
                confirmButtonColor: "#5cb85c",
                cancelButtonText: "Cancel",
                closeOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) form.submit();
            });
        }
    });

    // Initializing other modals
    $("#myTable").on('click','.btnDelete',function(){
         $(this).closest('tr').remove();
      });

      $("#labour_charge").change( function(){
        if($(this).val() == '1') {
            $("#dataRow2").show();
        } else {
            $('#dataRow2').hide();
        }
      });

      $('#client').on('change', function() {
        var client = $('#client').val();
            $.ajax({
                type: "GET",
                url: "../../products/fetch-client-prices/"+client,
                data: { client: client },
                success: function(response){
                    $('#product_id').html(response);
                }
            });
        });

        $("#product_id").on('change', function () {
            var product_id = $("#product_id").val();
            $.ajax({
                type: "GET",
                url: "../../products/price-lists/details/"+product_id,
                data: { product_id: product_id },
                success: function(response){
                    $("#dataRows").show();
                    var html = '<tr class="target">';
                    html += '<td>'+'<input type="hidden" name="id[]" value="'+response[0].pid+'">'+'</td>';
                    html += '<td width="60px">'+response[0].item_name+'</td>';
                    html += '<td width="60px">'+'<input type="number" step="0.01" min="0" max="100" name="discount[]" class="form-control discount" size="5" value="0">'+'</td>';
                    html += '<td width="60px">'+'<input type="number" step="0.01" min="0" name="selling_price[]" class="form-control selling_price" value="'+response[0].sale_price+'">'+'</td>';
                    html += '<td>'+'<input type="number" step="1" min="1" name="qty[]" class="form-control qty" size="15" required>'+'</td>';
                    html += '<td>'+'<input type="text" class="form-control total_" name="total_charge[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows').append(html);
                }
            });
        });

        $("#labour").on('change', function () {
            var labour_id = $("#labour").val();
            $.ajax({
                type: "GET",
                url: "../../labours/details/"+labour_id,
                data: { labour_id: labour_id },
                success: function(response){
                    $("#dataRows2").show();
                    var html = '<tr class="target_3">';
                    html += '<input type="hidden" name="labour_name[]" value="'+response[0].labour+'">';
                    html += '<td width="100px">'+response[0].labour+'</td>';
                    html += '<td>'+'<input type="text" name="unit_charge[]" class="form-control charge" value="'+response[0].charge+'">'+'</td>';
                    html += '<td>'+'<input type="text" name="labour_qty[]"  class="form-control qty" size="5" required>'+'</td>';
                    html += '<td>'+'<input type="text" class="form-control subtotal_other" name="total_amount[]" readonly value="0">'+'</td>';
                    html += '<td>'+'<button type="button" class="btn-sm btn-danger btnDelete"> <i class="fa fa-trash"></i> </button>'+'</td></tr>';
                    $('#dataRows2').append(html);
                }
            });
        });

    function calculate_price() {
        var price = 0;
        $(".qty").each(function() {
            rowindex = $(this).closest('tr').index();
            qty =  $(this).val();
            selling_price = $('table.order-list tbody tr:nth-child(' + (rowindex + 1) + ') .selling_price').val();
            price += qty * unit_price;
        });
        $('#totalCost').html(price);
    }

 function calculateItemsValue() {
    var total = 0;
    for (i=1; i<=total_items; i++) {
        itemID = document.getElementById("qty"+i);
        price = document.getElementById("selling_price"+i);
        if (typeof itemID === 'undefined' || itemID === null) {
            alert("No such item - " + "qty_"+i);
        } else {
            total = total + parseInt(itemID.value) * parseInt(price.value);
        }
    }
    document.getElementById("totalCost").innerHTML = "TZS" + total;  
}

        $("#save_btn").on('click', function () {
            var product_id = $("#product_id").val();
            var qty = $("#qty").val();
            var vehicle_reg = $('#vehicle_reg').val();
            var client_name = $("#client_name").val();
            var bill_no = $("#bill_no").val();
            var created_date = $(".created_date").val();
                $.ajax({
                        type: "POST",
                        url: "/pos/create",
                        data: { product_id: product_id, qty: qty, bill_no: bill_no, vehicle_reg: vehicle_reg, client_name: client_name, created_date: created_date },
                        success: function(response){
                            $('#display_message').html(response);
                            swal({
                                title: "Success!",
                                text: "You have Added New Sale",
                                type: "success",
                                confirmButtonClass: "btn-success",
                            });

                            $('input[type]="text", select', this).val('');
                            $('#formPost')[0].reset();
                            setTimeout(function() 
                            {
                                location.reload();
                            }, 1000)
                        }
                });
    });

    function CalculateItemsValue() {
    var total = 0;
    for (i=1; i<=total_items; i++) {
        itemID = document.getElementById("qnt_"+i);
        if (typeof itemID === 'undefined' || itemID === null) {
            alert("No such item - " + "qnt_"+i);
        } else {
            total = total + parseInt(itemID.value) * parseInt(itemID.getAttribute("data-price"));
        }
    }
    document.getElementById("ItemsTotal").innerHTML = "$" + total;
}

  });
</script>
@endsection