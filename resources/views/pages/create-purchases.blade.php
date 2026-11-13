@extends('layouts.app_header')

@section('content')

<style>
    /* ================= PURCHASE PAGE DESIGN ================= */

    .purchase-page {
        padding-bottom: 25px;
    }

    .purchase-page .panel-custom {
        border: 1px solid #e7ebef;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 3px 15px rgba(0, 0, 0, .035);
    }

    .purchase-page .panel-heading {
        padding: 20px 24px;
        background: #f7f8fa;
        border-bottom: 1px solid #e8ebef;
    }

    .purchase-page .panel-title1 {
        margin: 0;
        color: #344054;
        font-size: 18px;
        font-weight: 700;
    }

    .purchase-page .panel-heading small {
        display: block;
        margin-top: 6px;
        color: #8792a2;
        font-size: 12px;
    }

    .purchase-page .panel-body {
        padding: 25px;
    }

    .purchase-page .step-indicator {
        margin-bottom: 15px;
    }

    .purchase-section {
        padding: 22px;
        margin-bottom: 22px;
        background: #fff;
        border: 1px solid #e7ebef;
        border-radius: 7px;
    }

    .purchase-section-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 22px;
        padding-bottom: 13px;
        border-bottom: 1px solid #edf0f3;
        color: #374151;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .purchase-section-heading i {
        color: #8491a2;
        font-size: 16px;
    }

    .purchase-section-description {
        display: block;
        margin-top: -14px;
        margin-bottom: 20px;
        color: #929baa;
        font-size: 11px;
    }

    .purchase-page .form-group {
        margin-bottom: 17px;
    }

    .purchase-page .control-label {
        display: block;
        margin-bottom: 8px;
        color: #46505e;
        font-size: 12px;
        font-weight: 600;
    }

    .purchase-page .form-control {
        height: 41px;
        padding: 9px 12px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
        color: #354052;
        background: #fff;
        box-shadow: none;
        font-size: 13px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .purchase-page .form-control:focus {
        border-color: #8b99a9;
        box-shadow: 0 0 0 3px rgba(100, 116, 139, .08);
    }

    .purchase-page .form-control::placeholder {
        color: #a0a8b4;
        font-size: 12px;
    }

    /* ---- Numeric validation error state ---- */
    .purchase-page .form-control.has-error {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .12);
    }

    .purchase-field-error {
        display: none;
        margin-top: 4px;
        color: #b45353;
        font-size: 11px;
        font-weight: 500;
        line-height: 1.35;
    }

    .purchase-page .input-group-addon {
        background: #f5f6f8;
        border-color: #dce1e7;
        color: #64748b;
    }

    .purchase-page .select2-container {
        width: 100% !important;
    }

    .purchase-page .select2-container .select2-selection--single {
        height: 41px;
        border: 1px solid #dce1e7;
        border-radius: 5px;
    }

    .purchase-page .select2-container .select2-selection__rendered {
        line-height: 39px;
        padding-left: 12px;
        color: #354052;
        font-size: 13px;
    }

    .purchase-page .select2-container .select2-selection__arrow {
        height: 39px;
    }

    .purchase-item-row {
        position: relative;
        padding: 17px 10px 5px;
        margin-bottom: 14px;
        background: #fafbfc;
        border: 1px solid #e8ebef;
        border-radius: 6px;
    }

    .purchase-item-row:first-child {
        background: #fff;
    }

    .purchase-item-row .form-group {
        margin-bottom: 12px;
    }

    .purchase-item-row .control-label {
        font-size: 11px;
    }

    .purchase-item-row .form-control {
        height: 39px;
        padding: 8px 9px;
        font-size: 12px;
    }

    .purchase-item-actions {
        display: flex;
        align-items: flex-end;
        min-height: 65px;
        padding-bottom: 12px;
    }

    .purchase-page .add_field_button,
    .purchase-page .remove_field {
        min-height: 36px;
        padding: 8px 12px;
        border: 0;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .purchase-page .add_field_button {
        width: 100%;
        background: #526b60;
        color: #fff;
    }

    .purchase-page .add_field_button:hover {
        background: #40564c;
    }

    .purchase-page .remove_field {
        position: absolute;
        top: 8px;
        right: 10px;
        min-height: 29px;
        padding: 5px 10px;
        background: #fc4120;
        border: 1px solid #e0e3e7;
        color: #f0f2f5;
    }

    .purchase-page .remove_field:hover {
        background: #e7e9ed;
    }

    .purchase-page .remove_field i {
        margin-right: 4px;
    }

    .purchase-page .supplier-grid {
        margin-bottom: 5px;
    }

    .purchase-page .vat-options {
        padding: 14px 15px;
        background: #fafbfc;
        border: 1px solid #e8ebef;
        border-radius: 6px;
        min-height: 65px;
    }

    .purchase-page .vat-options .radio {
        display: inline-block;
        margin: 3px 18px 3px 0;
    }

    .purchase-page .vat-options .radio label {
        color: #566273;
        font-size: 12px;
        font-weight: 600;
    }

    .purchase-page .vat-amount-wrapper {
        margin-top: 12px;
    }

    .purchase-page input[type="file"].form-control {
        height: auto;
        min-height: 41px;
        padding: 8px;
    }

    .purchase-form-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        padding-top: 18px;
        border-top: 1px solid #edf0f3;
    }

    .purchase-save-btn {
        min-width: 145px;
        padding: 11px 22px;
        border: 1px solid #526b60;
        border-radius: 5px;
        background: #526b60;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        transition: all .2s ease;
    }

    .purchase-save-btn:hover {
        background: #40564c;
        border-color: #40564c;
        color: #fff;
    }

    .purchase-save-btn i {
        margin-right: 6px;
    }

    .purchase-required {
        color: #b45353;
    }

    @media (max-width: 991px) {
        .purchase-item-row {
            padding-top: 35px;
        }

        .purchase-item-actions {
            min-height: auto;
            padding-bottom: 12px;
        }
    }

    @media (max-width: 767px) {
        .purchase-page .panel-body {
            padding: 14px;
        }

        .purchase-page .panel-heading {
            padding: 17px;
        }

        .purchase-section {
            padding: 15px;
        }

        .purchase-item-row {
            padding: 40px 12px 5px;
        }

        .purchase-item-row .form-group {
            margin-bottom: 13px;
        }

        .purchase-form-footer {
            display: block;
        }

        .purchase-save-btn {
            width: 100%;
        }
    }
</style>

<div class="purchase-page">

    <!-- BREADCRUMB -->
    <div class="row">
        <div class="col-lg-12">
            <div class="step-indicator" style="float:right">
                <a class="step completed" href="#">Home</a>
                <a class="step" href="#">Purchases</a>
            </div>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="row">
        <div class="col-lg-12">

            <div class="panel panel-custom panel-border">

                <div class="panel-heading">
                    <h3 class="panel-title1">
                        <i class="fa fa-shopping-cart"></i>
                        Create Purchases
                    </h3>
                    <small>
                        Record purchased items, supplier details and invoice information.
                    </small>
                </div>

                <div class="panel-body">

                    <form id="purchase-form"
                          method="POST"
                          action="{{ url('send-purchases') }}"
                          enctype="multipart/form-data">

                        @csrf

                        <!-- ================= PURCHASE ITEMS ================= -->
                        <div class="purchase-section">

                            <div class="purchase-section-heading">
                                <i class="fa fa-cubes"></i>
                                Purchase Items
                            </div>

                            <span class="purchase-section-description">
                                Enter the details of each item being purchased.
                                You can add multiple items.
                            </span>

                            <div id="details">

                                <!-- FIRST ITEM -->
                                <div class="purchase-item-row">

                                    <div class="row">

                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Item Name <span class="purchase-required">*</span>
                                                </label>

                                                <select name="product_id[]"
                                                        class="form-control select2 purchase-item-select"
                                                        id="product_id"
                                                        required>
                                                    <option value="">Select Item</option>

                                                    @foreach($items as $p)
                                                        <option value="{{ $p->id }}">
                                                            {{ $p->item_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Make <span class="purchase-required">*</span>
                                                </label>

                                                <input type="text"
                                                       name="make[]"
                                                       id="make"
                                                       class="form-control"
                                                       placeholder="Make"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Model
                                                </label>

                                                <input type="text"
                                                       name="model[]"
                                                       id="model"
                                                       class="form-control"
                                                       placeholder="Model">
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Part Number
                                                </label>

                                                <input type="text"
                                                       name="part_number[]"
                                                       class="form-control"
                                                       placeholder="Part number">
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Purchase Price <span class="purchase-required">*</span>
                                                </label>

                                                <input type="text"
                                                       name="purchase_price[]"
                                                       id="purchase_price"
                                                       class="form-control"
                                                       placeholder="Purchase Price"
                                                       inputmode="decimal"
                                                       autocomplete="off"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-1 col-sm-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    Qty <span class="purchase-required">*</span>
                                                </label>

                                                <input type="text"
                                                       name="quantity[]"
                                                       id="quantity"
                                                       class="form-control"
                                                       placeholder="Qty"
                                                       inputmode="numeric"
                                                       autocomplete="off"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-1 col-sm-4">
                                            <div class="purchase-item-actions">
                                                <button class="add_field_button"
                                                        type="button">
                                                    <i class="fa fa-plus"></i>
                                                    Add
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- DYNAMIC ITEMS -->
                                <div class="input_fields"></div>

                            </div>
                        </div>

                        <!-- ================= SUPPLIER DETAILS ================= -->
                        <div id="client_details" class="purchase-section">

                            <div class="purchase-section-heading">
                                <i class="fa fa-truck"></i>
                                Supplier & Invoice Details
                            </div>

                            <div class="row supplier-grid">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Supplier Name
                                            <span class="purchase-required">*</span>
                                        </label>

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

                              <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        Invoice Number
                                        <span class="purchase-required">*</span>
                                    </label>

                                    <div class="input-group">
                                        <input type="text"
                                            name="invoice_number"
                                            id="invoice_number"
                                            class="form-control"
                                            placeholder="Enter invoice number or click Generate"
                                            autocomplete="off"
                                            required>
                                        <span class="input-group-btn">
                                            <button type="button"
                                                    class="btn btn-default"
                                                    id="generate_invoice_btn"
                                                    title="Auto-generate a unique invoice number"
                                                    style="border:1px solid #dce1e7;border-left:0;border-radius:0 5px 5px 0;height:41px;">
                                                <i class="fa fa-magic"></i> Generate
                                            </button>
                                        </span>
                                    </div>
                                    <small id="invoice_hint" class="help-block" style="display:none;margin-top:6px;"></small>
                                </div>
                        </div>
                           <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Upload Invoice File
                                        </label>

                                        <input type="file"
                                               name="invoice_file"
                                               id="invoice_file"
                                               class="form-control">
                                    </div>
                                </div>
                            </div>

                            <!-- VAT OPTIONS -->

                            <!-- INVOICE FILE AND DATE -->
                            <div class="row">
      <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            VAT Type
                                        </label>

                                        <div class="vat-options">

                                            <div class="radio radio-info radio-inline">
                                                <input type="radio"
                                                       name="vat_type"
                                                       id="with_vat1"
                                                       value="1"
                                                       checked
                                                       required>

                                                <label for="with_vat1">
                                                    With VAT
                                                </label>
                                            </div>

                                            <div class="radio radio-inline">
                                                <input type="radio"
                                                       name="vat_type"
                                                       id="without_vat"
                                                       value="0"
                                                       required>

                                                <label for="without_vat">
                                                    Without VAT
                                                </label>
                                            </div>

                                        </div>


                                    </div>
                                </div>


                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            Purchase Date
                                            <span class="purchase-required">*</span>
                                        </label>

                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control created_date"
                                                   autocomplete="off"
                                                   name="created_date"
                                                   placeholder="YYYY-MM-DD"
                                                   id="datepicker-autoclose"
                                                   data-date-format="yyyy-mm-dd"
                                                   required>

                                            <span class="input-group-addon">
                                                <i class="ti-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- ================= SAVE ================= -->
                        <div class="purchase-form-footer">

                            <button type="submit"
                                    class="purchase-save-btn"
                                    id="purchase_save_btn">

                                <i class="fa fa-save"></i>
                                Save Purchase

                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>


<!-- ================= DYNAMIC ITEM TEMPLATE ================= -->
<template id="purchaseItemTemplate">

    <div class="purchase-item-row added-purchase-item">

        <button type="button" class="remove_field">
            <i class="fa fa-trash"></i> Remove
        </button>

        <div class="row">

            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Item Name <span class="purchase-required">*</span>
                    </label>

                    <select name="product_id[]"
                            class="form-control purchase-item-select"
                            required>
                        <option value="">Select Item</option>

                        @foreach($items as $p)
                            <option value="{{ $p->id }}">
                                {{ $p->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Make <span class="purchase-required">*</span>
                    </label>

                    <input type="text"
                           name="make[]"
                           class="form-control"
                           placeholder="Make"
                           required>
                </div>
            </div>

            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Model
                    </label>

                    <input type="text"
                           name="model[]"
                           class="form-control"
                           placeholder="Model">
                </div>
            </div>

            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Part Number
                    </label>

                    <input type="text"
                           name="part_number[]"
                           class="form-control"
                           placeholder="Part Number">
                </div>
            </div>

            <div class="col-md-2 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Purchase Price <span class="purchase-required">*</span>
                    </label>

                    <input type="text"
                           name="purchase_price[]"
                           class="form-control"
                           placeholder="Purchase Price"
                           inputmode="decimal"
                           autocomplete="off"
                           required>
                </div>
            </div>

            <div class="col-md-1 col-sm-4">
                <div class="form-group">
                    <label class="control-label">
                        Qty <span class="purchase-required">*</span>
                    </label>

                    <input type="text"
                           name="quantity[]"
                           class="form-control"
                           placeholder="Qty"
                           inputmode="numeric"
                           autocomplete="off"
                           required>
                </div>
            </div>

        </div>
    </div>

</template>

<script>
    /* Route helpers – used below */
    window.PURCHASE_ROUTES = {
        checkInvoice:    @json(route('purchases.check-invoice')),
        generateInvoice: @json(route('purchases.generate-invoice')),
    };
</script>
<!-- ================= JAVASCRIPT ================= -->
<script>
$(document).ready(function () {

    /* ================= CSRF ================= */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /* ============================================================
     * SELECT2 HELPERS — v3 & v4 compatible
     * ============================================================ */
    function isSelect2Initialized($el) {
        if ($el.data('select2')) return true;
        if ($el.hasClass('select2-hidden-accessible')) return true;
        if ($el.next('.select2-container').length) return true;
        if ($el.attr('id') && $('#s2id_' + $el.attr('id')).length) return true;
        return false;
    }

    function initSelect2On($elements) {
        if (!$.fn.select2) return;
        $elements.each(function () {
            var $el = $(this);
            if (isSelect2Initialized($el)) return;
            try {
                $el.select2({ width: '100%' });
            } catch (err) {
                console.warn('Select2 init failed for element:', this, err);
            }
        });
    }

    initSelect2On($('.purchase-page .select2, .purchase-item-select'));


    /* ============================================================
     * ADD / REMOVE ITEM ROWS — delegated handlers
     * ============================================================ */
    var MAX_ITEMS = 30;

    $(document).on('click', '.add_field_button', function (e) {
        e.preventDefault();

        var currentItems = $('.purchase-item-row').length;
        if (currentItems >= MAX_ITEMS) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('You can add a maximum of ' + MAX_ITEMS + ' items per purchase.');
            } else {
                alert('You can add a maximum of ' + MAX_ITEMS + ' items per purchase.');
            }
            return;
        }

        var $newItem = null;
        var templateEl = document.getElementById('purchaseItemTemplate');

        if (templateEl && templateEl.content) {
            var fragment = templateEl.content.cloneNode(true);
            $newItem = $('<div>').append(fragment).children('.purchase-item-row').first();
        }

        if ((!$newItem || !$newItem.length) && templateEl) {
            var html = $('#purchaseItemTemplate').html();
            if (html) $newItem = $(html);
        }

        if (!$newItem || !$newItem.length) {
            console.error('Could not clone purchase item template.');
            alert('Something went wrong adding the row. Please refresh the page.');
            return;
        }

        $('.input_fields').append($newItem);
        initSelect2On($newItem.find('.purchase-item-select'));
        $newItem.find('select, input').first().trigger('focus');
    });

    $(document).on('click', '.remove_field', function (e) {
        e.preventDefault();

        var $row = $(this).closest('.purchase-item-row');

        $row.find('.purchase-item-select').each(function () {
            var $el = $(this);
            if (isSelect2Initialized($el)) {
                try { $el.select2('destroy'); } catch (err) { /* ignore */ }
            }
        });

        $row.remove();
    });


    /* ============================================================
     * VAT TYPE TOGGLE
     * ============================================================ */
    function toggleVatField() {
        var vatType = $('input[name="vat_type"]:checked').val();

        if (vatType === '1') {
            $('#vat_amount_wrapper').stop(true, true).slideDown(150);
            $('#vat_field').prop('disabled', false);
        } else {
            $('#vat_amount_wrapper').stop(true, true).slideUp(150);
            $('#vat_field').val('').prop('disabled', true);
        }
    }

    $(document).on('change', 'input[name="vat_type"]', toggleVatField);
    toggleVatField();


    /* ============================================================
     * NUMERIC VALIDATION — Purchase Price & Qty
     *   Price: positive number, optional decimals (e.g. 1500 or 1500.50)
     *   Qty  : positive whole number (e.g. 1, 5, 100)
     * ============================================================ */
    function validateNumberField($input) {
        var value  = ($input.val() || '').toString().trim();
        var isQty  = $input.attr('name') === 'quantity[]';

        // Ensure an inline error placeholder exists right after the input
        var $error = $input.siblings('.purchase-field-error');
        if ($error.length === 0) {
            $error = $('<span class="purchase-field-error"></span>').insertAfter($input);
        }

        // Empty → let HTML5 `required` handle it (don't complain here)
        if (value === '') {
            $input.removeClass('has-error');
            $error.hide().text('');
            if ($input[0] && $input[0].setCustomValidity) {
                $input[0].setCustomValidity('');
            }
            return true;
        }

        // Regex checks
        var isValid;
        if (isQty) {
            isValid = /^\d+$/.test(value) && parseInt(value, 10) > 0;
        } else {
            isValid = /^\d+(\.\d+)?$/.test(value) && parseFloat(value) > 0;
        }

        if (isValid) {
            $input.removeClass('has-error');
            $error.hide().text('');
            if ($input[0] && $input[0].setCustomValidity) {
                $input[0].setCustomValidity('');
            }
            return true;
        }

        var msg = isQty
            ? 'Qty must be a whole number (digits only — no letters or symbols).'
            : 'Price must be a number (e.g. 1500 or 1500.50 — no letters or symbols).';

        $input.addClass('has-error');
        $error.text(msg).show();
        if ($input[0] && $input[0].setCustomValidity) {
            $input[0].setCustomValidity(msg);
        }
        return false;
    }

    // Delegated: fires on typing AND paste for any current or future row
    $(document).on('input change', 'input[name="purchase_price[]"], input[name="quantity[]"]', function () {
        validateNumberField($(this));
    });


    /* ============================================================
     * INVOICE NUMBER — auto-generate button
     * ============================================================ */
    $('#generate_invoice_btn').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: window.PURCHASE_ROUTES.generateInvoice,
            type: 'GET',
            success: function (response) {
                if (response.invoice_number) {
                    $('#invoice_number').val(response.invoice_number).trigger('change');
                    $('#invoice_hint').hide();

                    if (typeof toastr !== 'undefined') {
                        toastr.success('Generated invoice #: ' + response.invoice_number);
                    }
                }
            },
            error: function () {
                alert('Could not generate an invoice number. Please try again.');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-magic"></i> Generate');
            }
        });
    });


    /* ============================================================
     * INVOICE NUMBER — duplicate check on blur
     * ============================================================ */
    var invoiceCheckTimer = null;
    $('#invoice_number').on('blur', function () {
        var invoiceNumber = $(this).val().trim();
        var $hint         = $('#invoice_hint');

        $hint.hide().removeClass('text-danger text-success').text('');
        if (!invoiceNumber) return;

        if (invoiceCheckTimer) clearTimeout(invoiceCheckTimer);
        invoiceCheckTimer = setTimeout(function () {

            $.ajax({
                url: window.PURCHASE_ROUTES.checkInvoice,
                type: 'GET',
                data: { invoice_number: invoiceNumber },
                success: function (response) {

                    if (!response.exists) {
                        $hint
                            .removeClass('text-danger')
                            .addClass('text-success')
                            .text('✓ Invoice number is available.')
                            .show();
                        return;
                    }

                    // Duplicate found → prompt to auto-generate
                    swal({
                        title: "Invoice already exists!",
                        text: "Invoice '" + invoiceNumber + "' already exists in this branch.\n\n" +
                              "Would you like to generate an automatic invoice number instead?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, Generate",
                        confirmButtonColor: "#526b60",
                        cancelButtonText: "No, I'll type my own"
                    }, function (confirm) {

                        if (confirm) {
                            $('#generate_invoice_btn').trigger('click');
                        } else {
                            $('#invoice_number').focus().select();
                            $hint
                                .removeClass('text-success')
                                .addClass('text-danger')
                                .text('This invoice number already exists. Please change it.')
                                .show();
                        }
                    });
                },
                error: function () { /* silent — server side will catch it */ }
            });
        }, 300);
    });


    /* ============================================================
     * CONSOLIDATED SUBMIT HANDLER
     *   1. Validate every Purchase Price & Qty
     *   2. Block if invoice number is marked duplicate
     *   3. Only then enable the "Saving..." spinner
     * ============================================================ */
    $('#purchase-form').off('submit').on('submit', function (e) {

        // ---- 1. Number validation ----
        var hasNumberError = false;
        var $firstBadField = null;

        $(this).find('input[name="purchase_price[]"], input[name="quantity[]"]').each(function () {
            if (!validateNumberField($(this))) {
                hasNumberError = true;
                if (!$firstBadField) $firstBadField = $(this);
            }
        });

        if (hasNumberError) {
            e.preventDefault();
            if ($firstBadField) {
                $firstBadField.trigger('focus');
                // Scroll into view nicely
                var offset = $firstBadField.offset();
                if (offset) {
                    $('html, body').animate({ scrollTop: Math.max(0, offset.top - 120) }, 200);
                }
            }
            return false;
        }

        // ---- 2. Invoice duplicate-hint guard ----
        var $hint = $('#invoice_hint');
        if ($hint.hasClass('text-danger') && $hint.is(':visible')) {
            e.preventDefault();
            if (typeof swal !== 'undefined') {
                swal({
                    title: "Invoice number invalid",
                    text: "The invoice number is marked as duplicate. " +
                          "Please fix it, or click Generate to auto-generate one.",
                    type: "warning",
                    confirmButtonColor: "#526b60"
                });
            } else {
                alert('Please fix the invoice number before saving.');
            }
            $('#invoice_number').trigger('focus').select();
            return false;
        }

        // ---- 3. All good → enable spinner ----
        if (this.checkValidity()) {
            $('#purchase_save_btn')
                .prop('disabled', true)
                .html('<i class="fa fa-spinner fa-spin"></i> Saving Purchase...');
        }
    });

});
</script>

@endsection