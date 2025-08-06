<div class="form-group">
    <label for="employee_id">Employee</label>
    <select id="employee_id" name="employee_id" class="form-control select2" required {{ isset($loan) ? 'disabled' : '' }}>
        <option value="">Select Name</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}"
                {{ (isset($loan) && $loan->employee_id == $employee->id) ? 'selected' : '' }}>
                {{ $employee->firstname }} {{ $employee->middlename }} {{ $employee->surname }}
            </option>
        @endforeach
    </select>

    @if (isset($loan))
        {{-- Hidden input to submit employee_id if select is disabled --}}
        <input type="hidden" name="employee_id" value="{{ $loan->employee_id }}">
    @endif
</div>

<div class="form-group">
    <label for="type">Loan Type</label>
    <input type="text" name="type" id="type" class="form-control"
           value="{{ old('type', $loan->type ?? '') }}"
           placeholder="Eg. Personal, Emergency" required>
</div>

<div class="form-group">
    <label for="principal">Amount (TZS)</label>
    <input type="number" step="0.01" name="principal" id="principal" class="form-control"
           value="{{ old('principal', $loan->principal ?? '') }}" required>
</div>

<div class="form-group">
    <label for="monthly_deduction">Monthly Deduction (TZS)</label>
    <input type="number" step="0.01" name="monthly_deduction" id="monthly_deduction" class="form-control"
           value="{{ old('monthly_deduction', $loan->monthly_deduction ?? '') }}" required>
</div>

<div class="form-group">
    <label for="balance">Outstanding Balance (TZS)</label>
    <input type="number" step="0.01" name="balance" id="balance" class="form-control"
           value="{{ old('balance', $loan->balance ?? 0) }}" required>
</div>

<div class="form-group">
    <label for="start_date">Start Date</label>
    <input type="month" name="start_date" id="start_date" class="form-control"
           value="{{ old('start_date', isset($loan) ? \Carbon\Carbon::parse($loan->start_date)->format('Y-m') : '') }}" required>
</div>
