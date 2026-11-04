<table class="table table-bordered table-striped" id="advancesTable">
    <thead>
        <tr>
            <th>Employee</th>
            <th>Amount (TZS)</th>
            <th>Month</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @forelse($salary_advances ?? [] as $advance)
        @php
            [$advYear, $advMonth] = array_pad(explode('-', $advance->month), 2, null);
        @endphp
        <tr class="advance-row"
            data-year="{{ $advYear }}"
            data-month="{{ $advMonth }}">
            <td>
                @if ($advance->employee)
                    {{ $advance->employee->firstname }} {{ $advance->employee->surname }}
                @else
                    <em>No employee</em>
                @endif
            </td>
            <td>{{ number_format($advance->amount, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($advance->month . '-01')->format('M Y') }}</td>
            <td>
                <button class="btn btn-xs btn-info" data-toggle="modal"
                        data-target="#editAdvanceModal{{ $advance->id }}">Edit</button>

                <form method="POST"
                      action="{{ route('salary-advances.destroy', $advance->id) }}"
                      style="display:inline;">
                    @csrf @method('DELETE')
                    <button class="btn btn-xs btn-danger"
                            onclick="return confirm('Delete this advance?')">Delete</button>
                </form>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="4" class="text-center text-muted">
                No salary advances found.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

@foreach($salary_advances ?? [] as $advance)
    <div class="modal fade" id="editAdvanceModal{{ $advance->id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST"
                      action="{{ route('salary-advances.update', $advance->id) }}">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4>Edit Salary Advance</h4>
                    </div>
                    <div class="modal-body">
                        @include('payrolls.advance_form_fields', ['advance' => $advance])
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success">Update</button>
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach