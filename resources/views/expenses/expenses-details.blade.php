@extends('layouts.app_header')

@section('content')
<style>
th {
  font-weight: 400;
  background: #7093cc;
  color: #FFF;
  text-transform: uppercase;
  font-size: 0.8em;
  font-family: 'Raleway', sans-serif;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
        <a class="btn btn-sm btn-info" href="{{ url('accounts/expenditures/petty-cash') }}"> <i class="ti-arrow-left"></i> &nbsp; Go Back</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-titl"> Petty Cash Details </h3> </div>
            <div class="panel-body">
<section>
    <div class="container">
        <table id="datatable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense Name</th>
                    <th>{{('Description')}}</th>
                    <th>{{('Voucher No.')}} </th>
                    <th>{{('Amount')}}</th>
                    <th>{{('Paid To')}}</th>
                    <th>{{('Date')}}</th>
                    <th>{{('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($expenses as $key=>$expense)
                <tr>
                    <td width="15">{{$key+1}}</td>
                    <td>{{ $expense->name }}</td>
                    <td>{{ $expense->note }}</td>
                    <td>{{ $expense->voucher_no }}</td>
                    <td>{{ number_format((float)$expense->amount, 2) }}</td>
                    <td>{{ $expense->paid_to }}</td>
                    <td>{{ $expense->created_date }}</td>
                    <td> 
                    <!-- <a class="btn btn-sm btn-success" href="#"><i class="fa fa-edit"></i> </a> -->
                    <button type="button" name="submit" data-id="{{ $expense->id }}" class="btn btn-sm btn-success waves-effect waves-light edit-details"><i class="fa fa-edit"></i> </button>
                    @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                    <a class="btn btn-sm btn-danger" href="{{ url('accounts/expenditures/petty-cash/delete-by-id/'.$expense->id) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                    @else 
                    @endif
                </td>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>
</div>
</section>


<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg editModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('accounts/update-petty-cash')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Edit Expenditure </h4>
                    </div>
                    <div class="modal-body">
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                    <input type="hidden" name="id" id="id" class="form-control">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Description  </label>
                                <input type="text" name="note" id="note" class="form-control">      
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Amount </label>
                                <input type="text" name="amount" id="amount" class="form-control">
                            </div>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Paid To  </label>
                                <input type="text" name="paid_to" id="paid_to" class="form-control" placeholder="Enter name of Person .." required>      
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Voucher No.  </label>
                                <input type="text" name="voucher_no" id="voucher_no" class="form-control" placeholder="Enter Voucher No..">      
                            </div>
                        </div>
                   </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                    </div>
                    <div class="row m-t-30">
                    <div class="col-md-1"></div>
                   </div>

                    <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button> -->
                        <button type="submit" name="submit" class="btn-rounded btn-success waves-effect waves-light">Update Expense </button>
                    </div>
                </form>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
</div>
<script type="text/javascript">

    $("ul#expense").siblings('a').attr('aria-expanded','true');
    $("ul#expense").addClass("show");
    $("ul#expense #exp-list-menu").addClass("active");

    var expense_id = [];
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        $('.edit-details').on('click', function() {
            var url = "../../../accounts/expenditures/petty-cash/"
            var id = $(this).data('id').toString();
            url = url.concat(id).concat("/edit");
            
            $.get(url, function(data) {
                $('.editModal').modal('show');
                $("#note").val(data['note']);
                $("#amount").val(data['amount']);
                $("#paid_to").val(data['paid_to']);
                $("#voucher_no").val(data['voucher_no']);
                $("#id").val(data['id']);
                $(".created_date").val(data['created_date']);
            });
        });
    })

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    }
    return false;
}

</script>
@endsection
