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

    /* ============================================================
       CASH COLLECTION MODAL (unchanged)
       ============================================================ */
    .cash-collection-modal {
        border: 0;
        border-radius: 8px;
        overflow: visible;
        box-shadow: 0 12px 38px rgba(80, 90, 100, .12);
    }

    .cash-modal-header {
        padding: 22px 25px;
        background: #f7f9fb;
        border-bottom: 1px solid #e7ebef;
        border-radius: 8px 8px 0 0;
    }

    .cash-modal-heading {
        display: flex;
        align-items: center;
        gap: 14px;
        padding-right: 25px;
    }

    .cash-modal-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e9f1f8;
        color: #315b7d;
        border-radius: 10px;
        font-size: 21px;
    }

    .cash-modal-header .modal-title {
        font-size: 19px;
        font-weight: 600;
        color: #344454;
        margin: 0 0 5px;
    }

    .cash-modal-heading p {
        margin: 0;
        color: #7c8995;
        font-size: 12px;
    }

    .cash-modal-header .close {
        font-size: 25px;
        opacity: .6;
    }

    .cash-collection-modal .modal-body {
        padding: 25px;
    }

    .cash-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #526273;
        padding-bottom: 13px;
        margin-bottom: 18px;
        border-bottom: 1px solid #edf0f3;
    }

    .cash-section-title i {
        margin-right: 7px;
    }

    .cash-collection-modal .form-group {
        margin-bottom: 22px;
    }

    .cash-collection-modal .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #4e5c69;
    }

    .cash-collection-modal .form-control {
        min-height: 42px;
        border: 1px solid #dce2e8;
        border-radius: 5px;
        box-shadow: none;
        padding: 10px 12px;
        color: #465563;
    }

    .cash-collection-modal .form-control:focus {
        border-color: #91a9bf;
        box-shadow: 0 0 0 2px rgba(85, 125, 160, .08);
    }

    .cash-collection-modal textarea.form-control {
        min-height: 90px;
        resize: vertical;
    }

    .cash-collection-modal .input-group-addon {
        background: #f5f7f9;
        border-color: #dce2e8;
        color: #687785;
        border-radius: 5px 0 0 5px;
    }

    .cash-collection-modal .input-group .form-control {
        border-radius: 0 5px 5px 0;
    }

    .cash-reference-display {
        min-height: 42px;
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 10px 12px;
        border: 1px dashed #d4dce4;
        background: #f8fafb;
        border-radius: 5px;
        color: #687785;
        font-size: 12px;
    }

    .cash-reference-display i {
        color: #73899b;
    }

    .cash-collection-modal .help-block {
        margin: 6px 0 0;
        font-size: 11px;
        color: #87929d;
    }

    .cash-info-note {
        padding: 12px 14px;
        background: #f7f9fb;
        border: 1px solid #e7ebef;
        border-radius: 5px;
        color: #6e7c89;
        font-size: 12px;
        line-height: 1.6;
    }

    .cash-info-note i {
        margin-right: 6px;
    }

    .cash-modal-footer {
        padding: 16px 25px;
        border-top: 1px solid #edf0f3;
        background: #fcfcfd;
        border-radius: 0 0 8px 8px;
    }

    .cash-modal-footer .btn {
        padding: 9px 16px;
        border-radius: 5px;
        font-size: 12px;
    }

    .cash-modal-footer .btn i {
        margin-right: 5px;
    }

    /* ============================================================
       BULK EXPENSE MODAL — custom large dialog
       (same pattern as cash-collection-dialog)
       ============================================================ */
    #addExpenseModal .expense-bulk-dialog {
        width: 95%;
        max-width: 1400px;
        margin: 30px auto;
    }

    #addExpenseModal .cash-collection-modal {
        width: 100%;
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 15px 45px rgba(80, 90, 100, 0.15);
    }

    #addExpenseModal .modal-body {
        padding: 30px 35px;
    }

    #addExpenseModal .cash-modal-header {
        padding: 25px 35px;
    }

    #addExpenseModal .cash-modal-footer {
        padding: 18px 35px;
    }

    /* Shared header fields inside the bulk modal */
    .bulk-shared-fields {
        background: #f7f9fb;
        border: 1px solid #e7ebef;
        border-radius: 6px;
        padding: 18px 20px 4px;
        margin-bottom: 22px;
    }

    .bulk-shared-fields .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #4e5c69;
        margin-bottom: 8px;
    }

    .bulk-shared-fields .form-control {
        min-height: 44px;
        padding: 10px 12px;
        font-size: 13px;
        border-radius: 5px;
        border: 1px solid #dce2e8;
        box-shadow: none;
        color: #465563;
    }

    .bulk-shared-fields .form-control:focus {
        border-color: #91a9bf;
        box-shadow: 0 0 0 2px rgba(85, 125, 160, .08);
    }

    .bulk-expense-table-wrapper {
        width: 100%;
        overflow-x: auto;
        border: 1px solid #e2e8ef;
        border-radius: 7px;
        background: #fff;
    }

    .bulk-expense-table {
        width: 100%;
        min-width: 920px;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .bulk-expense-table thead th {
        background: #f0f4f9;
        color: #4e5c69;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .045em;
        padding: 13px 10px;
        border: 0;
        border-bottom: 1px solid #dfe6ed;
        white-space: nowrap;
    }

    .bulk-expense-table tbody td {
        padding: 10px;
        border: 0;
        border-bottom: 1px solid #edf0f3;
        vertical-align: middle;
        background: #fff;
    }

    .bulk-expense-table tbody tr:last-child td { border-bottom: 1px solid #e2e8ef; }
    .bulk-expense-table tbody tr:hover td { background: #fafbfd; }
    .bulk-expense-table .row-number {
        width: 42px;
        text-align: center;
        color: #758392;
        font-size: 12px;
        font-weight: 700;
    }

    .bulk-expense-table .form-control {
        min-height: 40px;
        padding: 8px 10px;
        font-size: 13px;
        border-radius: 5px;
        border: 1px solid #dce2e8;
        box-shadow: none;
        color: #465563;
    }

    .bulk-expense-table .form-control:focus {
        border-color: #91a9bf;
        box-shadow: 0 0 0 2px rgba(85, 125, 160, .08);
    }

    .bulk-expense-table .btn-remove-row {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .bulk-expense-table tfoot td {
        padding: 13px 12px;
        background: #f7f9fb;
        border: 0;
        color: #4e5c69;
        font-size: 12px;
        font-weight: 700;
    }

    .bulk-expense-table tfoot .grand-total-value {
        color: #1d6f42;
        font-size: 15px;
        white-space: nowrap;
    }

    .bulk-expense-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 14px;
        padding: 13px 15px;
        background: #f8fafc;
        border: 1px solid #e7ebef;
        border-radius: 7px;
    }

    .bulk-expense-bottom .row-counter {
        color: #71808e;
        font-size: 12px;
        font-weight: 600;
    }

    .bulk-expense-bottom .btn { border-radius: 5px; padding: 9px 15px; }
    .bulk-expense-bottom .btn i { margin-right: 6px; }

    /* ============================================================
       CASH COLLECTION — custom large dialog
       ============================================================ */
    #cashCollectionModal .cash-collection-dialog {
        width: 90%;
        max-width: 1100px;
        margin: 30px auto;
    }

    #cashCollectionModal .cash-collection-modal {
        width: 100%;
        border: 0;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 15px 45px rgba(80, 90, 100, 0.15);
    }

    #cashCollectionModal .modal-body {
        padding: 30px 35px;
    }

    #cashCollectionModal .cash-modal-header {
        padding: 25px 35px;
    }

    #cashCollectionModal .cash-modal-footer {
        padding: 18px 35px;
    }

    #cashCollectionModal .form-group {
        margin-bottom: 25px;
    }

    #cashCollectionModal .form-control {
        min-height: 44px;
    }

    #cashCollectionModal textarea.form-control {
        min-height: 100px;
    }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 991px) {
        #cashCollectionModal .cash-collection-dialog {
            width: 95%;
            max-width: none;
        }

        #cashCollectionModal .modal-body {
            padding: 22px;
        }

        #addExpenseModal .expense-bulk-dialog {
            width: 96%;
            max-width: none;
        }

        #addExpenseModal .modal-body {
            padding: 22px;
        }

        #addExpenseModal .cash-modal-header,
        #addExpenseModal .cash-modal-footer {
            padding: 20px 22px;
        }
    }

    @media (max-width: 767px) {
        #cashCollectionModal .cash-collection-dialog,
        #addExpenseModal .expense-bulk-dialog {
            width: auto;
            margin: 10px;
        }

        #cashCollectionModal .modal-body,
        #addExpenseModal .modal-body {
            padding: 18px;
        }

        #cashCollectionModal .cash-modal-header,
        #cashCollectionModal .cash-modal-footer,
        #addExpenseModal .cash-modal-header,
        #addExpenseModal .cash-modal-footer {
            padding: 16px 18px;
        }
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
                <h3 class="panel-titl"> Petty Cash </h3>
            </div>
            <div class="panel-body">
                <section>
                    <div class="container">

                        <button type="button"
                                class="btn btn-primary w-md waves-effect waves-light m-b-20 m-t-10"
                                style="float: right; margin-left: 10px;"
                                data-toggle="modal"
                                data-target="#cashCollectionModal">
                            <i class="fa fa-plus"></i> Add Cash Collection
                        </button>

                        <button type="button"
                                class="btn btn-info w-md waves-effect waves-light m-b-20 m-t-10"
                                style="float: right"
                                data-toggle="modal"
                                data-target="#addExpenseModal">
                            + Add Expense
                        </button>

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
                            @foreach($expenses as $key => $expense)
                                <tr>
                                    <td width="20"> {{ $key+1 }} </td>
                                    <td>{{ $expense->created_date }}</td>
                                    <td>{{ number_format((float)$expense->total_amount, 2) }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-info"
                                           href="{{ url('accounts/expenditures/petty-cash/'.$expense->created_date) }}">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @if (Auth::user()->role_id == 1 OR Auth::user()->role_id == 2)
                                            <a class="btn btn-sm btn-danger"
                                               href="{{ url('accounts/expenditures/petty-cash/delete-by-date/'.$expense->created_date) }}"
                                               onclick="return confirm('Are you sure you want to delete this item?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            </div>
        </div>
    </div>
</div>

{{-- ================= CASH COLLECTION MODAL ================= --}}
<div class="modal fade"
     id="cashCollectionModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="cashCollectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog cash-collection-dialog" role="document">

        <form action="{{ url('/accounts/add-cash-collection') }}"
              method="POST"
              id="cashCollectionForm">

            @csrf

            <div class="modal-content cash-collection-modal">

                <div class="modal-header cash-modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <div class="cash-modal-heading">
                        <span class="cash-modal-icon">
                            <i class="fa fa-money"></i>
                        </span>

                        <div>
                            <h4 class="modal-title"
                                id="cashCollectionModalLabel">
                                Add Cash Collection
                            </h4>

                            <p>Record money received into your account.</p>
                        </div>
                    </div>
                </div>

                <div class="modal-body">

                    <div class="cash-section-title">
                        <i class="fa fa-file-text-o"></i>
                        Collection Details
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="collection_account_id">
                                    Account <span class="text-danger">*</span>
                                </label>

                                <select name="account_id"
                                        id="collection_account_id"
                                        class="form-control select2"
                                        style="width:100%;"
                                        required>

                                    <option value="">Select Account</option>

                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}"
                                            {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                            ({{ $account->account_no }})
                                        </option>
                                    @endforeach

                                </select>

                                @error('account_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="collection_amount">
                                    Amount <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-addon">TSh</span>

                                    <input type="number"
                                           name="amount"
                                           id="collection_amount"
                                           class="form-control"
                                           min="0.01"
                                           step="0.01"
                                           value="{{ old('amount') }}"
                                           placeholder="0.00"
                                           required>
                                </div>

                                @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="collection_date">
                                    Collection Date <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       name="created_date"
                                       id="collection_date"
                                       class="form-control"
                                       value="{{ old('created_date', date('Y-m-d')) }}"
                                       required>

                                @error('created_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                        <label for="collection_note">Note / Description</label>

                        <textarea name="note"
                                  id="collection_note"
                                  class="form-control"
                                  rows="3"
                                  maxlength="255"
                                  placeholder="Describe the reason or source of this collection...">{{ old('note') }}</textarea>

                        @error('note')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                    </div>
</div>
                </div>

                <div class="modal-footer cash-modal-footer">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="saveCashCollectionBtn">
                        <i class="fa fa-save"></i>
                        Save Collection
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

{{-- ================= BULK ADD EXPENSES MODAL — custom large dialog ================= --}}
<div class="modal fade"
     id="addExpenseModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="addExpenseLabel">

    <div class="modal-dialog expense-bulk-dialog" role="document">

        <form action="{{ url('/accounts/add-petty-cash') }}"
              method="POST"
              id="bulkExpenseForm">

            @csrf
            <input type="hidden" name="branch_id" value="{{ Auth::user()->branch_id }}">

            <div class="modal-content cash-collection-modal">

                {{-- Header --}}
                <div class="modal-header cash-modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <div class="cash-modal-heading">
                        <span class="cash-modal-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </span>

                        <div>
                            <h4 class="modal-title" id="addExpenseLabel">
                                Add Expenses
                            </h4>
                            <p>One account, one date, one voucher &mdash; many line items.</p>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body">

                    {{-- ============ SHARED HEADER FIELDS ============ --}}
                    <div class="cash-section-title">
                        <i class="fa fa-info-circle"></i>
                     Details
                    </div>

                    <div class="bulk-shared-fields">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="bulk_account_id">
                                        Account <span class="text-danger">*</span>
                                    </label>

                                    <select name="account_id"
                                            id="bulk_account_id"
                                            class="form-control select2"
                                            style="width:100%;"
                                            required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}"
                                                {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                                                {{ $acc->name }} ({{ $acc->account_no }})
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('account_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="bulk_created_date">
                                        Date <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           name="created_date"
                                           id="bulk_created_date"
                                           class="form-control"
                                           value="{{ old('created_date', date('Y-m-d')) }}"
                                           required>

                                    @error('created_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="bulk_voucher_no">Voucher No</label>

                                    <input type="text"
                                           name="voucher_no"
                                           id="bulk_voucher_no"
                                           class="form-control"
                                           value="{{ old('voucher_no') }}"
                                           placeholder="Optional">

                                    @error('voucher_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============ EXPENSE ITEMS TABLE ============ --}}
                    <div class="cash-section-title">
                        <i class="fa fa-list"></i>
                        Expense Items
                    </div>

                    <div class="bulk-expense-table-wrapper">
                        <table class="table bulk-expense-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 22%;">Category <span class="text-danger">*</span></th>
                                    <th style="width: 16%;">Amount <span class="text-danger">*</span></th>
                                    <th style="width: 28%;">Description <span class="text-danger">*</span></th>
                                    <th style="width: 23%;">Paid To <span class="text-danger">*</span></th>
                                    <th style="width: 6%; text-align:center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="expense-rows"></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">GRAND TOTAL</td>
                                    <td colspan="2"><span class="grand-total-value">TSh <span id="row-total">0.00</span></span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="bulk-expense-bottom">
                        <span class="row-counter" id="row-counter">0 rows added</span>
                        <button type="button" class="btn btn-primary" id="add-row-btn">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer cash-modal-footer">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Save Expense
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>

{{-- ================= EXPENSE ROW TEMPLATE ================= --}}
<table style="display:none" aria-hidden="true">
    <tbody>
        <tr id="expense-row-template">
            <td class="row-number"><span class="row-number-value">1</span></td>
            <td>
                <select name="expenses[__INDEX__][expense_category_id]" class="form-control expense-category" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01"
                       name="expenses[__INDEX__][amount]"
                       class="form-control row-amount"
                       placeholder="0.00" inputmode="decimal" required>
            </td>
            <td>
                <input type="text" name="expenses[__INDEX__][note]"
                       class="form-control" maxlength="255"
                       placeholder="Description" required>
            </td>
            <td>
                <input type="text" name="expenses[__INDEX__][paid_to]"
                       class="form-control" maxlength="191"
                       placeholder="Paid to" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-remove-row" title="Remove this row" aria-label="Remove this expense row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>

<script type="text/javascript">
    $(function () {
        $("ul#expense").siblings('a').attr('aria-expanded', 'true');
        $("ul#expense").addClass("show");
        $("ul#expense #exp-list-menu").addClass("active");

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        /* Existing expense-category edit dialog */
        $('.open-Editexpense_categoryDialog').on('click', function () {
            var id = String($(this).data('id'));
            $.get("expenses/" + id + "/edit", function (data) {
                $('#editModal #reference').text(data.reference_no);
                $("#editModal select[name='expense_category_id']").val(data.expense_category_id);
                $("#editModal select[name='account_id']").val(data.account_id);
                $("#editModal input[name='amount']").val(data.amount);
                $("#editModal input[name='expense_id']").val(data.id);
                $("#editModal textarea[name='note']").val(data.note);
                if ($.fn.selectpicker) $('.selectpicker').selectpicker('refresh');
            });
        });

        var rowIndex = 0;
        var submittingBulkExpense = false;

        function initSelect2($element) {
            if ($.fn.select2) {
                $element.each(function () {
                    var $select = $(this);
                    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
                    $select.select2({ width: '100%', dropdownParent: $('#addExpenseModal') });
                });
            }
        }

        function addExpenseRow() {
            var html = $('#expense-row-template').prop('outerHTML')
                .replace('id="expense-row-template"', '')
                .replace(/__INDEX__/g, rowIndex);
            var $row = $(html);
            $('#expense-rows').append($row);
            initSelect2($row.find('.expense-category'));
            rowIndex++;
            updateRowMeta();
            $row.find('select, input').first().trigger('focus');
        }

        function updateRowMeta() {
            var $rows = $('#expense-rows tr');
            $rows.each(function (index) {
                $(this).find('.row-number-value').text(index + 1);
            });
            var count = $rows.length;
            $('#row-counter').text(count + (count === 1 ? ' expense row added' : ' expense rows added'));
            updateLineTotal();
        }

        function updateLineTotal() {
            var total = 0;
            $('#expense-rows .row-amount').each(function () {
                var value = parseFloat($(this).val());
                if (Number.isFinite(value) && value > 0) total += value;
            });
            $('#row-total').text(total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }

        $('#addExpenseModal').on('shown.bs.modal', function () {
            if ($('#expense-rows tr').length === 0) addExpenseRow();
            initSelect2($('#bulk_account_id'));
        });

        $('#addExpenseModal').on('hidden.bs.modal', function () {
            if ($.fn.select2) $('#expense-rows select.select2-hidden-accessible').select2('destroy');
            $('#expense-rows').empty();
            rowIndex = 0;
            submittingBulkExpense = false;
            $('#bulkExpenseForm')[0].reset();
            $('#bulkExpenseForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-save"></i> Save Expense');
            $('#bulk_account_id').val('').trigger('change');
            updateRowMeta();
        });

        $('#add-row-btn').on('click', function () { addExpenseRow(); });

        $(document).on('click', '#expense-rows .btn-remove-row', function () {
            if ($('#expense-rows tr').length <= 1) {
                alert('At least one expense row is required.');
                return;
            }
            var $row = $(this).closest('tr');
            if ($row.find('select.select2-hidden-accessible').length && $.fn.select2) {
                $row.find('select.select2-hidden-accessible').select2('destroy');
            }
            $row.remove();
            updateRowMeta();
        });

        $(document).on('input change', '#expense-rows .row-amount', updateLineTotal);

        $('#bulkExpenseForm').on('submit', function (event) {
            if ($('#expense-rows tr').length === 0) {
                event.preventDefault();
                alert('Please add at least one expense row.');
                return false;
            }
            if (submittingBulkExpense) {
                event.preventDefault();
                return false;
            }
            submittingBulkExpense = true;
            $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        });
    });

    function confirmDelete() {
        return confirm('Are you sure you want to delete?');
    }
</script>
@endsection