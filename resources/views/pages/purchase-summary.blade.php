
@extends('layouts.app_header')

@section('content')

<style>
/* =====================================================
   PURCHASE HISTORY - PROFESSIONAL DESIGN
===================================================== */

.purchase-history-page {
    padding-bottom: 30px;
    color: #4b5563;
    font-family: inherit;
}

.purchase-history-page .page-breadcrumb {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 18px;
}

.purchase-history-page .panel {
    background: #fff;
    border: 1px solid #e8ebef;
    border-radius: 8px;
    box-shadow: 0 3px 14px rgba(0,0,0,.035);
    overflow: hidden;
}

.purchase-history-page .panel-heading {
    background: #fff;
    border-bottom: 1px solid #edf0f2;
    padding: 20px 23px;
}

.purchase-history-page .heading-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.purchase-history-page .heading-title {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #303946;
}

.purchase-history-page .heading-subtitle {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #929aa5;
}

.purchase-history-page .heading-icon {
    width: 40px;
    height: 40px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #e8ebef;
    border-radius: 7px;
    color: #566273;
    margin-right: 10px;
    font-size: 16px;
}

.purchase-history-page .panel-body {
    padding: 22px;
}

.purchase-history-page .table-responsive {
    border: none;
    margin-bottom: 0;
}

.purchase-history-page #datatable {
    width: 100% !important;
    margin-bottom: 0;
    border: 1px solid #e9edf1;
    border-collapse: separate;
    border-spacing: 0;
}

.purchase-history-page #datatable thead th {
    background: #f6f7f9;
    color: #626d7a;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .45px;
    text-transform: uppercase;
    padding: 15px 13px;
    border: 0;
    border-bottom: 1px solid #e7ebef;
    white-space: nowrap;
}

.purchase-history-page #datatable tbody td {
    padding: 14px 13px;
    vertical-align: middle;
    font-size: 13px;
    color: #596270;
    border: 0;
    border-bottom: 1px solid #edf0f3;
}

.purchase-history-page #datatable tbody tr:last-child td {
    border-bottom: 0;
}

.purchase-history-page #datatable tbody tr:hover {
    background: #fafbfc;
}

.purchase-history-page .row-number {
    color: #929ba7;
    font-size: 12px;
    font-weight: 600;
}

.purchase-history-page .supplier-name {
    color: #364252;
    font-weight: 600;
    text-decoration: none;
}

.purchase-history-page .supplier-name:hover,
.purchase-history-page .invoice-link:hover {
    text-decoration: underline;
}

.purchase-history-page .invoice-link {
    color: #52667e;
    font-weight: 600;
    text-decoration: none;
}

.purchase-history-page .amount-value {
    color: #364252;
    font-weight: 700;
    white-space: nowrap;
}

.purchase-history-page .date-value {
    white-space: nowrap;
    color: #687382;
}

.purchase-history-page .action-buttons {
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

.purchase-history-page .action-buttons .btn {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e6eb;
    border-radius: 6px;
    background: #fff;
    color: #5d6876;
    font-size: 13px;
    transition: all .2s ease;
}

.purchase-history-page .action-buttons .btn:hover {
    background: #f3f5f7;
    border-color: #cfd5dc;
    color: #303946;
}

.purchase-history-page .action-buttons .btn-delete {
    color: #b94a48;
}

.purchase-history-page .action-buttons .btn-delete:hover {
    background: #fff2f1;
    border-color: #edcfcc;
    color: #a83b38;
}

/* =====================================================
   EDIT MODAL
===================================================== */

#edit_details_modal .modal-dialog {
    margin-top: 65px;
}

#edit_details_modal .modal-content {
    border: none;
    border-radius: 9px;
    overflow: hidden;
    box-shadow: 0 12px 45px rgba(0,0,0,.16);
}

#edit_details_modal .modal-header {
    padding: 20px 23px;
    background: #fff;
    border-bottom: 1px solid #edf0f3;
}

#edit_details_modal .modal-title {
    color: #303946;
    font-size: 16px;
    font-weight: 700;
}

#edit_details_modal .modal-title i {
    color: #687789;
    margin-right: 7px;
}

#edit_details_modal .modal-body {
    padding: 24px;
}

#edit_details_modal .modal-footer {
    padding: 15px 23px;
    background: #fafbfc;
    border-top: 1px solid #edf0f3;
}

#edit_details_modal .form-group {
    margin-bottom: 19px;
}

#edit_details_modal label {
    display: block;
    margin-bottom: 8px;
    color: #515d6b;
    font-size: 12px;
    font-weight: 700;
}

#edit_details_modal .form-control {
    height: 41px;
    border: 1px solid #dfe4e9;
    border-radius: 5px;
    box-shadow: none;
    color: #424c59;
    font-size: 13px;
}

#edit_details_modal .form-control:focus {
    border-color: #9ba8b6;
    box-shadow: 0 0 0 2px rgba(110,125,145,.08);
}

#edit_details_modal .form-control[readonly] {
    background: #f6f7f9;
    color: #7d8794;
}

#edit_details_modal .input-group-addon {
    background: #f6f7f9;
    border-color: #dfe4e9;
    color: #687585;
}

#edit_details_modal .vat-options {
    min-height: 41px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

#edit_details_modal .vat-options label {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin: 0;
    font-weight: 500;
}

#edit_details_modal .vat-options input {
    margin: 0;
}

#edit_details_modal .file-note {
    margin-top: 7px;
    font-size: 11px;
    color: #8b95a1;
}

#edit_details_modal .btn {
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    padding: 9px 16px;
}

#edit_details_modal .btn-update {
    min-width: 125px;
}

#edit_details_modal .loading-message {
    display: none;
    padding: 25px 10px;
    text-align: center;
    color: #697584;
    font-size: 13px;
}

#edit_details_modal .loading-message i {
    margin-right: 7px;
}

/* Select2 modal compatibility */
#edit_details_modal .select2-container {
    width: 100% !important;
}

#edit_details_modal .select2-container .select2-selection--single {
    height: 41px;
    border: 1px solid #dfe4e9;
    border-radius: 5px;
}

#edit_details_modal .select2-container--default
.select2-selection--single .select2-selection__rendered {
    line-height: 39px;
    padding-left: 12px;
    color: #424c59;
    font-size: 13px;
}

#edit_details_modal .select2-container--default
.select2-selection--single .select2-selection__arrow {
    height: 39px;
}

@media(max-width: 767px) {
    .purchase-history-page .panel-body {
        padding: 12px;
    }

    .purchase-history-page .panel-heading {
        padding: 16px;
    }

    .purchase-history-page .page-breadcrumb {
        justify-content: flex-start;
    }

    #edit_details_modal .modal-dialog {
        margin: 15px;
    }

    #edit_details_modal .modal-body {
        padding: 17px;
    }
}
</style>


<div class="purchase-history-page">

    <!-- BREADCRUMB -->
    <div class="row">
        <div class="col-md-12">
            <div class="page-breadcrumb">
                <div class="step-indicator">
                    <a class="step completed" href="{{ url('home') }}">Home</a>
                    <a class="step" href="#">Purchase History</a>
                </div>
            </div>
        </div>
    </div>


    <!-- PURCHASE HISTORY -->
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-custom panel-border">

                <div class="panel-heading">

                    <div class="heading-content">

                        <div class="row" style="margin:0; display:flex; align-items:center;">

                            <div class="heading-icon">
                                <i class="fa fa-shopping-cart"></i>
                            </div>

                            <div>
                                <h4 class="heading-title">Purchase History</h4>
                                <span class="heading-subtitle">
                                    View, edit and manage your purchase invoices
                                </span>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="panel-body">

                    <div class="table-responsive">

                        <table id="datatable"
                               class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Supplier Name</th>
                                    <th>Invoice Number</th>
                                    <th>Invoice Amount (VAT Incl.)</th>
                                    <th>Invoice Date</th>
                                    <th width="135">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($purchases as $key => $purchase)

                                <tr>

                                    <td>
                                        <span class="row-number">
                                            {{ $key + 1 }}
                                        </span>
                                    </td>

                                    <td>
                                        <a class="supplier-name"
                                           href="{{ url('/purchase-report/'.$purchase->created_date.'/'.$purchase->invoice_number) }}">
                                            {{ $purchase->supplier_name }}
                                        </a>
                                    </td>

                                    <td>
                                        <a class="invoice-link"
                                           href="{{ url('/attachments/'.$purchase->invoice_file) }}"
                                           target="_blank">
                                            {{ $purchase->invoice_number }}
                                        </a>
                                    </td>

                                    <td>
                                        <span class="amount-value">
                                            {{ number_format(($purchase->total + $purchase->calculated_vat_amount), 2) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="date-value">
                                            {{ $purchase->created_date }}
                                        </span>
                                    </td>

                                    <td>

                                        <div class="action-buttons">

                                            <!-- EDIT -->
                                            <button type="button"
                                                    class="btn edit-details"
                                                    title="Edit Purchase"
                                                    data-id="{{ $purchase->invoice_number }}">
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            <!-- VIEW -->
                                            <a href="{{ url('/purchase-report/'.$purchase->created_date.'/'.$purchase->invoice_number) }}"
                                               class="btn"
                                               title="View Purchase"
                                               target="_blank">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            <!-- DELETE -->
                                            <button type="button"
                                                    class="btn btn-delete delete-purchase"
                                                    title="Delete Purchase"
                                                    data-invoice="{{ $purchase->invoice_number }}"
                                                    data-url="{{ url('purchases/invoices/delete/'.$purchase->invoice_number) }}">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="6" class="text-center"
                                        style="padding:35px;color:#929aa5;">
                                        <i class="fa fa-folder-open"
                                           style="font-size:28px;display:block;margin-bottom:10px;"></i>
                                        No purchase records found.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>


<!-- =====================================================
     EDIT PURCHASE MODAL
===================================================== -->

<div class="modal fade"
     id="edit_details_modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="editPurchaseTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-md">

        <div class="modal-content">

            <form id="formUpdate"
                  method="POST"
                  action="{{ url('purchases/update-purchase-details') }}"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h4 class="modal-title" id="editPurchaseTitle">
                        <i class="fa fa-edit"></i>
                        Edit Purchase Details
                    </h4>

                </div>


                <div class="modal-body">

                    <div class="loading-message" id="purchaseLoading">
                        <i class="fa fa-spinner fa-spin"></i>
                        Loading purchase details...
                    </div>

                    <div id="purchaseEditFields">

                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Invoice Number</label>

                                    <input type="text"
                                           name="invoice_no"
                                           id="invoice_no"
                                           class="form-control"
                                           readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Invoice Date</label>

                                    <div class="input-group">

                                        <input type="text"
                                               class="form-control created_date"
                                               required
                                               autocomplete="off"
                                               name="created_date"
                                               placeholder="YYYY-MM-DD"
                                               id="datepicker-autoclose"
                                               data-date-format="yyyy-mm-dd">

                                        <span class="input-group-addon">
                                            <i class="ti-calendar"></i>
                                        </span>

                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier Name</label>

                                    <select name="supplier_id"
                                            class="form-control select2"
                                            id="supplier_id"
                                            required>

                                        <option value="">Select Supplier</option>

                                        @foreach($suppliers as $p)
                                            <option value="{{ $p->id }}">
                                                {{ $p->supplier_name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">

                                    <label>VAT Setting</label>

                                    <div class="vat-options">

                                        <label>
                                            <input type="radio"
                                                   name="vat_type"
                                                   id="vat_type1"
                                                   value="1">
                                            With VAT
                                        </label>

                                        <label>
                                            <input type="radio"
                                                   name="vat_type"
                                                   id="vat_type2"
                                                   value="0">
                                            Without VAT
                                        </label>

                                    </div>

                                </div>
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">

                                    <label>Invoice Attachment</label>

                                    <input type="file"
                                           name="invoice_file"
                                           id="invoice_file"
                                           class="form-control"
                                           accept=".pdf,application/pdf">

                                    <p class="file-note">
                                        <i class="fa fa-info-circle"></i>
                                        PDF format only. Leave empty to keep the existing invoice attachment.
                                    </p>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i>
                        Close
                    </button>

                    <button type="submit"
                            class="btn btn-success btn-update"
                            id="update_btn">
                        <i class="fa fa-save"></i>
                        Update Purchase
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =====================================================
     JAVASCRIPT
===================================================== -->

<script>
$(document).ready(function () {

    // CSRF CONFIGURATION
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // ============================================
    // EDIT PURCHASE
    // ============================================

    $(document).on('click', '.edit-details', function () {

        var invoiceId = $(this).data('id');

        if (!invoiceId) {
            swal('Error', 'Invoice number is missing.', 'error');
            return;
        }

        // Reset form
        $('#formUpdate')[0].reset();

        $('#invoice_no').val('');
        $('#supplier_id').val('').trigger('change');

        $('#purchaseEditFields').hide();
        $('#purchaseLoading').show();

        $('#edit_details_modal').modal('show');

        var editUrl = "{{ url('purchases/edit-details') }}/" +
                      encodeURIComponent(invoiceId);

        $.ajax({

            url: editUrl,
            type: 'GET',
            dataType: 'json',

            success: function (response) {

                if (!response || !response[0]) {

                    $('#purchaseLoading').hide();

                    swal(
                        'Not Found',
                        'Purchase details could not be found.',
                        'warning'
                    );

                    $('#edit_details_modal').modal('hide');
                    return;
                }

                var purchase = response[0];

                // Invoice details
                $('#invoice_no').val(purchase.invoice_number || '');
                $('.created_date').val(purchase.created_date || '');

                // Supplier
                $('#supplier_id')
                    .val(purchase.supplier_id || '')
                    .trigger('change');

                // VAT
                $('input[name="vat_type"]').prop('checked', false);

                if (String(purchase.vat_type) === '1') {
                    $('#vat_type1').prop('checked', true);
                } else {
                    $('#vat_type2').prop('checked', true);
                }

                $('#purchaseLoading').hide();
                $('#purchaseEditFields').show();

            },

            error: function (xhr) {

                $('#purchaseLoading').hide();

                console.error('Edit purchase error:', xhr.responseText);

                swal(
                    'Error',
                    'Unable to load purchase details. Please try again.',
                    'error'
                );

                $('#edit_details_modal').modal('hide');

            }

        });

    });


    // ============================================
    // DELETE PURCHASE - SWEETALERT
    // ============================================

    $(document).on('click', '.delete-purchase', function (e) {

        e.preventDefault();

        var deleteUrl = $(this).data('url');
        var invoiceNo = $(this).data('invoice');

        if (!deleteUrl) {
            swal('Error', 'Delete URL is missing.', 'error');
            return;
        }

        swal({

            title: 'Delete Purchase?',
            text: 'Are you sure you want to delete invoice ' +
                  invoiceNo + '? This action cannot be undone.',

            type: 'warning',

            showCancelButton: true,
            confirmButtonColor: '#b94a48',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',

            closeOnConfirm: false,
            showLoaderOnConfirm: true

        }, function (isConfirm) {

            if (isConfirm) {

                // Existing Laravel delete route
                window.location.href = deleteUrl;

            }

        });

    });


    // ============================================
    // UPDATE BUTTON LOADING
    // ============================================

    $('#formUpdate').on('submit', function () {

        if (this.checkValidity && !this.checkValidity()) {
            return;
        }

        $('#update_btn')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Updating...');

    });


    // ============================================
    // RESET MODAL WHEN CLOSED
    // ============================================

    $('#edit_details_modal').on('hidden.bs.modal', function () {

        $('#formUpdate')[0].reset();

        $('#purchaseLoading').hide();
        $('#purchaseEditFields').show();

        $('#update_btn')
            .prop('disabled', false)
            .html('<i class="fa fa-save"></i> Update Purchase');

    });

});
</script>

@endsection
