@extends('layouts.app_header')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="{{ url('home') }}">Home</a> 
            <a class="step" href="#"> ACCOUNTS </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h4 class="panel-title1"> ACCOUNT LIST  </h4>
                <div id="display_message" style="display: none"></div>
            </div>
            <div class="panel-body">
    <div class="container-fluid">
        <button class="btn btn-info add_account" style="float: right;" data-toggle="modal" data-target="#account-modal"><i class="fa fa-plus"></i> {{ 'Add Account'}}</button>
    </div>
    <div class="table-responsive">
        <table id="account-table" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ ('Account')}} No</th>
                    <th>{{ ('Account Name')}}</th>
                    <th>{{ ('Opening Balance')}}</th>
                    <th>{{ ('Available Balance')}}</th>
                    <th>{{ ('Description')}} </th>
                    <th>{{ ('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $key=>$account)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{ $account->account_no }}</td>
                    <td>{{ $account->name }}</td>
                    @if($account->initial_balance)
                        <td>{{ number_format((float)$account->initial_balance, 2) }}</td>
                    @else
                        <td>0.00</td>
                    @endif
                    <td>{{ number_format((float)$account->total_balance, 2) }}</td>
                    <td>{{ $account->note }}</td>
                    <td>
                    <button class="btn btn-primary btn-sm  waves-effect waves-light edit_account" data-id="{{ $account->id }}" data-toggle="modal" data-target=".bs-example-modal-sm"><i class="fa fa-plus"></i></button>
                    <a class="btn btn-sm btn-info" href="{{ url('accounts/cash-in-histories/'.$account->id) }}"><i class="fa fa-eye"></i></a>
                    @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                    <a class="btn btn-sm btn-danger" href="{{ url('accounts/delete/'.$account->id) }}" onclick="return confirm('Are you sure you want to delete this Account ?');"><i class="fa fa-trash"></i></a>
                    @else 
                    @endif    
                </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
</section>


<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="edit_modal">
      <form id="formUpdate" method="post" action="{{ url('accounts/add-cash') }}">
             @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title" id="myLargeModalLabel">Add Cash to Account  </h3> 
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Account Name </label>
                                <input type="text" name="account_name" id="account_name" class="form-control" placeholder="Opening Balance">
                             </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Account No. </label>
                                <input type="text" name="account_no" id="account_no" class="form-control" placeholder="Opening Balance">
                             </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Available Balance   </label>
                                <input type="hidden" name="id" id="id" class="form-control">
                                <input type="text" name="total_balance" id="total_balance" class="form-control" placeholder="Available Balance">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Amount   </label>
                                <input type="text" name="new_amount" id="new_amount" class="form-control" placeholder="Add Cash  Eg. 50000">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-info b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                     </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" style="float: left;" class="btn-lg btn-success waves-effect waves-light">Update </button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->
</div>



<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md addfund" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="addfund_modal">
      <form id="formUpdate" method="post" action="{{ url('accounts/addfund/update') }}">
             @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title" id="myLargeModalLabel">Add Fund  </h3> 
                    </div>
                    
                    <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Account Name </label>
                                <input type="text" name="account_name" id="account_name" class="form-control">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Available Balance   </label>
                                <!-- <input type="hidden" name="id" id="id_" class="form-control"> -->
                                <input type="text" name="total_balance" id="total_balance" class="form-control" placeholder="Available Balance">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Amount    </label>
                                <input type="hidden" name="id" id="id" class="form-control">
                                <input type="text" name="new_fund" id="new_fund" class="form-control" placeholder="Add Fund">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                    </div>
                   
                    <div class="modal-footer">
                        <button type="submit" style="float: left;" class="btn-lg btn-success waves-effect waves-light">Update </button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->
</div>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;" id="add_modal">
      <form id="formUpdate" method="post" action="{{ url('accounts/add') }}">
             @csrf
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h3 class="modal-title" id="myLargeModalLabel">Add Account  </h3> 
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Account Name   </label>
                                <input type="text" name="account_name" class="form-control" placeholder="Enter Account Name">
                             </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Note   </label>
                                <input type="text" name="note" class="form-control" placeholder="Eg. Petty Cash">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Account Number   </label>
                                <input type="text" name="account_no" id="account_no" class="form-control" placeholder="Enter Account No.">
                             </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Opening Balance   </label>
                                <input type="text" name="initial_balance" id="initial_balance" class="form-control" placeholder="Opening Balance">
                             </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" style="float: left;" class="btn-lg btn-info waves-effect waves-light">Save </button>
                    </div>
             </div><!-- /.modal-content -->        
            </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->


<script type="text/javascript">
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.edit_account').click(function() {
         var id = $(this).data('id');
         $.ajax({
             url: `../accounts/edit/${id}`,
             type: "GET",
             success: function (response) {
                 console.log(response[0]);
                 $('#edit_modal').modal('show');
                 $("#id").val(response[0].id);
                 $("#initial_balance").val(response[0].initial_balance);
                 $("#total_balance").val(response[0].total_balance);
                 $("#account_no").val(response[0].account_no);
                 $("#account_name").val(response[0].name);
                 $("#note").val(response[0].note);
             }
         });
    });

  $('.addfund').click(function() {
         var id = $(this).data('id');
         console.log('Fund', id);
         $.ajax({
             url: `../accounts/edit/${id}`,
             type: "GET",
             success: function (response) {
                 $('#addfund_modal').modal('show');
                 console.log(response[0]);
                //  $("#id_").val(response[0].id);
                 $("#initial_balance").val(response[0].initial_balance);
                 $("#total_balance").val(response[0].total_balance);
                 $("#account_no").val(response[0].account_no);
                 $("#account_name").val(response[0].name);
                 $("#note").val(response[0].note);

             }
         });
    });

  $('.add_account').click(function() {
    $('#add_modal').modal('show');
  });


    $("ul#account").siblings('a').attr('aria-expanded','true');
    $("ul#account").addClass("show");
    $("ul#account #account-list-menu").addClass("active");


    $('.default').on('change', function() {
        //off to on
        if ($(this).parent().hasClass("btn-success")) {
            var id = $(this).data('id');
            $('.default').not($(this)).parent().removeClass('btn-success');
            $('.default').not($(this)).parent().addClass('btn-danger off');
            $('.default').not($(this)).prop('checked', false);
            $(this).prop('checked', true);
            $.get('accounts/make-default/' + id, function(data) {
                alert(data);
            });
        }
        //on to off
        else {
            $(this).parent().removeClass('btn-danger off');
            $(this).parent().addClass('btn-success');
            $(this).prop('checked', true);
            alert('Please make another account default first!');
        }
    });

function confirmDelete() {
    if (confirm("Are you sure want to delete?")) {
        return true;
    }
    return false;
}

});

</script>
@endsection
