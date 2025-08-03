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
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#"> EXPENDITURES </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                <h3 class="panel-titl"> Petty Cash </h3> </div>
            <div class="panel-body">
<section>
    <div class="container">
    <button style="float: right" type="submit" class="btn btn-info w-md waves-effect waves-light m-b-20 m-t-10" id="" data-toggle="modal" data-target=".bs-example-modal-lg"> + Add Expenditure </button>
    </div><br/>
        <table id="datatable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{('Created date')}}</th>
                    <th>{{('Total Amount ')}}</th>
                    <th>{{('Action')}}</th>
                </tr>
            </thead>
            <tbody>
                 @foreach($expenses as $key=>$expense)
                <tr> 
                    <td width="20"> {{ $key+1 }} </td>
                    <td>{{ $expense->created_date }}</td>
                    <td>{{ number_format((float)$expense->total_amount, 2) }}</td>
                    <td> 
                    <a class="btn btn-sm btn-info" href="{{ url('accounts/expenditures/petty-cash/'.$expense->created_date) }}"><i class="fa fa-eye"></i> </a>
                    @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                    <a class="btn btn-sm btn-danger" href="{{ url('accounts/expenditures/petty-cash/delete-by-date/'.$expense->created_date) }}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash"></i></a>
                    @else 
                    @endif
                </td>
                </tr>
                @endforeach
            </tbody>
        </table>
</div>
</section>

<!--  Modal content for the above example -->
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
            <form  action="{{ url('/accounts/add-petty-cash')}}" method="POST" enctype="multipart/form-data"> 
                    @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myLargeModalLabel">Add Expenditure </h4>
                    </div>
                    <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}"  class="form-control">      
                    <div class="modal-body">
                    <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                            <!-- <div class="form-group">
                                <label for="inputEmail3" class="control-label">Expense Name  </label>
                                <input type="text" name="note" required class="form-control" placeholder="Expense Name ... Eg. stationery equipment" required>      
                            </div> -->
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Expense Category  * </label>
                                <select name="petty_category_id" required class="form-control select2" id="petty_category_id" required>
                                 <option value=""> Select Category </option> 
                                 @foreach($categories as $p)
                                   <option value="{{ $p->id }}"> {{ $p->name }} </option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Amount </label>
                                <input type="text" name="amount" id="amount" class="form-control" required placeholder="Amount .. Eg. 60000">
                            </div>
                    </div>
                    <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Description  </label>
                                <input type="text" name="note" required class="form-control" placeholder="Write Short notes Eg. Posho " >      
                            </div>
                        </div>
                 </div>
                 <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Paid To  </label>
                                <input type="text" name="paid_to" required class="form-control" placeholder="Enter name of Person .." >      
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label"> Voucher No.  </label>
                                <input type="text" name="voucher_no"  class="form-control" placeholder="Enter Voucher No..">      
                            </div>
                        </div>
                   </div>
                    <div class="row">
                    <div class="col-md-1"></div>
                       <div class="col-md-5">
                            <div class="form-group">
                                <label for="inputEmail3" class="control-label">Account  * </label>
                                <select name="account_id" required class="form-control select2" id="account_id" required>
                                   @foreach($accounts as $p)
                                   <option value="{{ $p->id }}"> {{ $p->name }}  ({{ $p->account_no }})</option>
                                   @endforeach
                             </select>        
                            </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                        <label for="inputEmail3" class="control-label">Date  *</label>
                        <div class="input-group">
                            <input type="text" class="form-control created_date" required autocomplete="off" name="created_date" placeholder="Date " id="datepicker-autoclose" data-date-format="yyyy-mm-dd">
                            <span class="input-group-addon bg-primary b-0 text-white"><i class="ti-calendar"></i></span>
                        </div><!-- input-group -->                           
                       </div>
                    </div>
                    </div>
                    <div class="row m-t-30">
                    <div class="col-md-1"></div>
                   </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light">Save </button>
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
        $('.open-Editexpense_categoryDialog').on('click', function() {
            var url = "expenses/"
            var id = $(this).data('id').toString();
            url = url.concat(id).concat("/edit");
            $.get(url, function(data) {
                $('#editModal #reference').text(data['reference_no']);
                $("#editModal select[name='expense_category_id']").val(data['expense_category_id']);
                $("#editModal select[name='account_id']").val(data['account_id']);
                $("#editModal input[name='amount']").val(data['amount']);
                $("#editModal input[name='expense_id']").val(data['id']);
                $("#editModal textarea[name='note']").val(data['note']);
                $('.selectpicker').selectpicker('refresh');
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
