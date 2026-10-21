@forelse($employees as $key => $employee)
    <tr>
        <td class="sn-col">{{ $key + 1 }}</td>

        <td>
            <div class="emp-name">
                <span class="emp-avatar">{{ strtoupper(substr($employee->firstname ?? '?', 0, 1)) }}</span>
                <span>{{ trim($employee->firstname . ' ' . $employee->middlename . ' ' . $employee->surname) }}</span>
            </div>
        </td>

        <td>{{ $employee->email ?? '—' }}</td>
        <td>{{ $employee->department_name ?? '—' }}</td>
        <td>{{ $employee->position ?? '—' }}</td>

        <td>
            @if($employee->contract_name)
                <span class="badge badge-pill badge-info">{{ $employee->contract_name }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

        <td>{{ $employee->mobile ?? '—' }}</td>

        <td>
            @if($employee->branch_name)
                <span class="badge badge-pill badge-light border">{{ $employee->branch_name }}</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>

        <td class="text-center">
            <div class="emp-actions">
                <a href="{{ url('staffs/managements/employees/' . $employee->id) }}"
                   class="btn-icon btn-view" title="View employee">
                    <i class="fa fa-eye"></i>
                </a>
                <a href="{{ url('staffs/managements/employees/' . $employee->id . '/edit') }}"
                   class="btn-icon btn-edit" title="Edit employee">
                    <i class="fa fa-pencil"></i>
                </a>
                <form action="{{ route('employees.destroy', $employee->id) }}"
                      method="POST" class="d-inline"
                      onsubmit="return confirm('Delete this employee?');">
                    @csrf @method('DELETE')
                    <button class="btn-icon btn-del" title="Delete employee" type="submit">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="text-center text-muted py-4">
            <i class="fa fa-inbox fa-2x d-block mb-2"></i>
            No employees found for the selected filters.
        </td>
    </tr>
@endforelse