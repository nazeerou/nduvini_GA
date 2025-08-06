<div class="form-group">
    <label>Employee</label>
    <select name="employee_id" class="form-control select2" required>
        <option value="">Select Employee</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}"
                {{ isset($advance) && $advance->employee_id == $employee->id ? 'selected' : '' }}>
                {{ $employee->firstname }} {{ $employee->surname }}
            </option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label>Amount (TZS)</label>
    <input type="number" step="0.01" min="0" name="amount" class="form-control"
           value="{{ $advance->amount ?? old('amount') }}" required>
</div>

<div class="form-group">
    <label>Month</label>
    <input type="month" name="month" class="form-control"
           value="{{ isset($advance) ? $advance->month : old('month') }}" required>
</div>
