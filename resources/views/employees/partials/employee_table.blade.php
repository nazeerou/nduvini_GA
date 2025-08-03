<table class="table table-striped table-bordered">
    <thead>
        <tr style="background: #eee;">
            <th>SN</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Designation</th>
            <th>Contract Type</th>
            <th>Mobile</th>
            <th>Branch</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $key => $employee)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->email }}</td>
            <td>{{ $employee->department->name ?? 'N/A' }}</td>
            <td>{{ $employee->designation->position ?? 'N/A' }}</td>
            <td>{{ $employee->contract->type->name ?? 'N/A' }}</td>
            <td>{{ $employee->mobile }}</td>
            <td>{{ $employee->branch->branch_name ?? 'N/A' }}</td>
            <td>
                <!-- actions here -->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
