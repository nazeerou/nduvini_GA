<a href="/payrolls/download/view/details/" class="btn btn-sm btn-pink" style="float: right;" target="_blank">
     <i class="fa fa-download"></i> Download PDF
</a>
 &nbsp;&nbsp;
<a href="/payrolls/download/view/details/" class="btn btn-sm btn-success" style="float: right;" target="_blank">
     <i class="fa fa-download"></i> Download Excel
</a> 
<hr/>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Member No</th>
            <th>FirstName</th>
            <th>MiddleName</th>
            <th>Surname</th>
            <th>Wage </th>
        </tr>
    </thead>
    <tbody>
    @foreach($slips as $key => $slip)
        <tr>
             <td>{{ $slip['nssf_no'] }}</td>
             <td>{{ $slip['firstname'] }}</td>
            <td>{{ strtoupper($slip['middlename']) }}</td>
            <td>{{ strtoupper($slip['surname']) }}</td>
            <td>{{ $slip['gross_salary'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
