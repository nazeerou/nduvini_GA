<a href="/payrolls/download/view/details/" class="btn btn-sm btn-pink" style="float: right;" target="_blank">
     <i class="fa fa-download"></i> Download PDF
</a> 
<hr/>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>SN</th>
            <th>Employee Name</th>
            <th>Bank Name</th>
            <th>Account Number</th>
            <th>Account Name </th>
            <th>Payment </th>
        </tr>
    </thead>
    <tbody>
    @foreach($slips as $key => $slip)
        <tr>
             <td>{{ $key+1 }}</td>
            <td>{{ strtoupper($slip['employee_name']) }}</td>
            <td>{{ $slip['bank_name'] }}</td>
            <td>{{ $slip['account_number'] }}</td>
            <td>{{ $slip['account_name'] }}</td>
            <td>{{ $slip['net_salary'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
