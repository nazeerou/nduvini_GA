@extends('layouts.app_header')

@section('content')

<style>
    .filter {
        border: 2px solid #DDDFE1;
        padding: 15px 10px 0 20px;
        margin-bottom: 0px;
    }
    th {
  /* font-weight: 400; */
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
    }
</style>
<!-- whole sale setting -->
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a> 
            <a class="step" href="#"> Purchase History </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> Purchase History  </h4> <br/>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                    </div>
                </div>
                <div class="row">
                <table id="datatable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th>Supplier Name </th>
                                <th>Reference Number</th>
                                <th>Invoice Number</th>
                                <th>Invoice Amount (VAT incl.)</th>
                                <th>Invoice Date</th>
                                <!-- <th>Invoice File </th> -->
                                <th width="160px">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($purchases as $key => $purchase)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td><a href="{{ url('/purchase-report/'.$purchase->created_date.'/'.$purchase->invoice_number) }}"> {{ $purchase->supplier_name }} </a> </td>
                                <td style="padding-left: 11px;">
                                 @if($purchase->lpo_number)
                                  {{ $purchase->lpo_number }}
                                 @else
                                 <a class="btn btn-sm" href="{{ url('purchases/lpo/'.$purchase->invoice_number.'/'.$purchase->created_date) }}">Create LPO </a> 
                                 @endif
                                </td>
                                <td> 
                                <a href="{{ url('/attachments/'.$purchase->invoice_file) }}" target="_blank">{{ $purchase->invoice_number }}</a>
                                </td>
                                <td> {{number_format(($purchase->total + $purchase->calculated_vat_amount), 2) }}</td>
                                <td>{{ $purchase->created_date}}</td>
                                <td> 
                                <button class="btn-rounded btn-success btn-sm  waves-effect waves-light edit-details" data-id="{{ $purchase->invoice_number }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-edit"></i></button>
                                <a href="{{ url('/purchase-report/'.$purchase->created_date.'/'.$purchase->invoice_number) }}" class="btn-rounded btn-sm btn-info waves-effect waves-light"> <i class="fa fa-eye"></i> </a>
                                <a class="btn-rounded btn-sm btn-danger"  href="{{ url('purchases/invoices/delete/'.$purchase->invoice_number) }}" onclick="return confirm('Are you sure yo want to delete ?'); "><i class="fa fa-trash"></i> </a>
                            </td>       
                            </tr>
                          @endforeach  
                            </tbody>
                        </table>
                   </div>
                </div><br/>
            </div>
        </div>
    </div>
</div>
<!-- END  -->

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="edit_details_modal">
      <form id="formUpdate" method="post" action="{{ url('purchases/update-purchase-details') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Purchase Details </h4>
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Invoice No#  </label>
                                <!-- <input type="text" name="id" id="id" class="form-control" placeholder=""> -->
                                <input type="text" name="invoice_no" id="invoice_no" readonly class="form-control" placeholder="">
                             </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date </label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date Supplied" id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                         </div>
                        </div>
                    </div>
                    <div class="row m-t-15 m-b-30">
                           <div class="col-md-6">
                           <div class="form-group">
                           <label for="inputEmail3" class="control-label">Supplier Name  </label>
                            <select name="supplier_id" class="form-control select2" id="supplier_id" required>
                                   @foreach($suppliers as $p)
                                   <option value="{{ $p->id }}"> {{ $p->supplier_name }} </option>
                                   @endforeach
                             </select>      
                            </div>
                           </div>
                           <div class="col-md-6">
                           <div class="form-group">
                           <label for="inputEmail3" class="control-label"> </label> <br/>
                        <div class="radio radio-info radio-inline">
                                        <input type="radio" name="vat_type" id="vat_type1" value="1" name="radioInline" checked>
                                        <label for="inlineRadio1"> With VAT </label>
                                    </div>
                                    <div class="radio radio-inline">
                                        <input type="radio" name="vat_type" id="vat_type2" value="0" name="radioInline" >
                                        <label for="inlineRadio2"> Without VAT </label>
                                  </div>
                               </div>
                           </div>
                       </div>

                       <div class="row m-t-15 m-b-30">
                        <div class="col-md-6">
                        <label for="inputEmail3" class="control-label"> Invoice File   </label>
                                <input type="file" name="invoice_file" id="invoice_file" class="form-control">
                           </div> <br/> NB: All File should be pdf format.
                       </div>
                    <div class="modal-footer">
                        <!-- <button type="button" class="btn-rounded btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" class="btn-rounded btn-success waves-effect waves-light" id="update_btn"> Update </button>
                    </div>
                 </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->


<script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit-details').click(function() {
         var invoice_id = $(this).data('id');
         $.ajax({
             url: `purchases/edit-details/${invoice_id}`,
             type: "GET",
             success: function (response) {
                 $('#edit_details_modal').modal('show');
                 $("#vat_amount").val(response[0].vat_amount);
                 $(".created_date").val(response[0].created_date);
                 $("#invoice_no").val(response[0].invoice_number);
                 $("#vat_type").val(response[0].vat_type);
                 $("#id").val(response[0].id);
             }
         });
    });


    $("#del_id").on('click', function (e) {
            e.preventDefault();
            swal({
            title: "Hey! ",
            text: "Are you sure want to delete ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            }).then((result) => {
                if(result) {
                    $("#del_event").submit();
                }
            }
        )
            ;
        });

    $("#deleteBtn").on('click', function(e) {
    e.preventDefault();
    var id = $(this).data('invoice_number');
    swal({
            title: "Are you sure you want to delete ?",
            type: "error",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            showCancelButton: true,
        },
        function() {
            $.ajax({
                type: "POST",
                url: "{{url('/destroy')}}",
                data: {id:id},
                success: function (data) {
                              //
                    }         
            });
    });
   });
  });
  
</script>


@endsection