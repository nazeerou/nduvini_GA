
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        {{ $transaction->exists ? 'Edit' : 'New' }} Petty Cash Transaction
    </title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        .page-wrapper {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .form-card {
            background: white;
            border-radius: 8px;
            padding: 28px;
            box-shadow: 0 3px 15px rgba(0,0,0,.07);
        }

        .page-title {
            font-size: 23px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        label {
            font-weight: 600;
            font-size: 13px;
        }

        .form-control {
            min-height: 40px;
        }

        .amount-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
        }

        .required {
            color: #dc3545;
        }
    </style>
</head>

<body>
<div class="page-wrapper">
    <div class="form-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="page-title">
                    {{ $transaction->exists ? 'Edit Transaction' : 'New Transaction' }}
                </div>

                <div class="text-muted">
                    Enter the details of the petty cash transaction.
                </div>
            </div>

            <a href="{{ route('account-transactions.index') }}"
               class="btn btn-outline-secondary">
                Back
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $transaction->exists
                    ? route('account-transactions.update', $transaction->id)
                    : route('account-transactions.store') }}">

            @csrf

            @if($transaction->exists)
                @method('PUT')
            @endif

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>
                        Transaction Date <span class="required">*</span>
                    </label>

                    <input type="date"
                           name="transaction_date"
                           class="form-control"
                           value="{{ old(
                               'transaction_date',
                               $transaction->exists
                                   ? $transaction->transaction_date->format('Y-m-d')
                                   : date('Y-m-d')
                           ) }}"
                           required>
                </div>

                <div class="form-group col-md-4">
                    <label>
                        Transaction Type <span class="required">*</span>
                    </label>

                    <select name="transaction_type"
                            id="transaction_type"
                            class="form-control"
                            required>

                        <option value="">Select Type</option>

                        @foreach([
                            'opening_balance' => 'Opening Balance',
                            'cash_collection' => 'Cash Collection',
                            'expense' => 'Expense',
                            'cash_payment' => 'Cash Payment',
                            'adjustment' => 'Adjustment',
                        ] as $value => $label)

                            <option value="{{ $value }}"
                                {{ old(
                                    'transaction_type',
                                    $transaction->transaction_type
                                ) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-4">
                    <label>Account ID</label>

                    <input type="number"
                           name="account_id"
                           min="1"
                           class="form-control"
                           value="{{ old('account_id', $transaction->account_id) }}"
                           placeholder="Optional">
                </div>
            </div>

            <div class="form-group">
                <label>
                    Particulars <span class="required">*</span>
                </label>

                <input type="text"
                       name="particulars"
                       class="form-control"
                       maxlength="500"
                       value="{{ old('particulars', $transaction->particulars) }}"
                       placeholder="e.g. Fuel, Cash Collection, Opening Balance"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Voucher Type</label>

                    <select name="voucher_type" class="form-control">
                        <option value="">Select Voucher Type</option>

                        @foreach([
                            'Opening Balance',
                            'Cash Receipt',
                            'Cash Payment',
                            'Petty Cash Contra',
                            'Journal Voucher',
                        ] as $voucher)

                            <option value="{{ $voucher }}"
                                {{ old(
                                    'voucher_type',
                                    $transaction->voucher_type
                                ) == $voucher ? 'selected' : '' }}>
                                {{ $voucher }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>Voucher Number</label>

                    <input type="text"
                           name="voucher_no"
                           class="form-control"
                           maxlength="255"
                           value="{{ old('voucher_no', $transaction->voucher_no) }}"
                           placeholder="Optional voucher number">
                </div>
            </div>

            <div class="form-group">
                <label>Expense Category</label>

                <select name="expense_category_id" class="form-control">
                    <option value="">Select Category (Optional)</option>

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ old(
                                'expense_category_id',
                                $transaction->expense_category_id
                            ) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="amount-box mb-4">
                <div class="form-row">
                    <div class="form-group col-md-6 mb-md-0">
                        <label>Debit Amount (TZS)</label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="debit"
                               id="debit"
                               class="form-control"
                               value="{{ old(
                                   'debit',
                                   $transaction->exists ? $transaction->debit : '0.00'
                               ) }}"
                               required>
                        <small class="text-muted">
                            Cash received or opening debit balance.
                        </small>
                    </div>

                    <div class="form-group col-md-6 mb-0">
                        <label>Credit Amount (TZS)</label>

                        <input type="number"
                               step="0.01"
                               min="0"
                               name="credit"
                               id="credit"
                               class="form-control"
                               value="{{ old(
                                   'credit',
                                   $transaction->exists ? $transaction->credit : '0.00'
                               ) }}"
                               required>
                        <small class="text-muted">
                            Cash paid out or opening credit balance.
                        </small>
                    </div>
                </div>

                <div id="amount-help" class="mt-3 small text-muted">
                    Enter a positive amount in either Debit or Credit, not both.
                </div>
            </div>

            <div class="form-group">
                <label>Description / Notes</label>

                <textarea name="description"
                          class="form-control"
                          rows="3"
                          maxlength="5000"
                          placeholder="Additional details (optional)">{{ old('description', $transaction->description) }}</textarea>
            </div>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="{{ route('account-transactions.index') }}"
                   class="btn btn-light mr-2">
                    Cancel
                </a>

                <button type="submit" class="btn btn-primary">
                    {{ $transaction->exists ? 'Update Transaction' : 'Save Transaction' }}
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    const debitInput = document.getElementById('debit');
    const creditInput = document.getElementById('credit');
    const typeInput = document.getElementById('transaction_type');
    const help = document.getElementById('amount-help');

    debitInput.addEventListener('input', function () {
        if (Number(this.value) > 0) {
            creditInput.value = '0.00';
        }
    });

    creditInput.addEventListener('input', function () {
        if (Number(this.value) > 0) {
            debitInput.value = '0.00';
        }
    });

    typeInput.addEventListener('change', function () {
        if (this.value === 'cash_collection' ||
            this.value === 'opening_balance') {
            help.textContent =
                'Normally recorded as a debit when cash increases.';
        } else if (this.value === 'expense' ||
                   this.value === 'cash_payment') {
            help.textContent =
                'Normally recorded as a credit when petty cash decreases.';
        } else {
            help.textContent =
                'Enter a positive amount in either Debit or Credit, not both.';
        }
    });
</script>

</body>
</html>
