
@extends('layouts.app_header')

@section('content')

<style>
    .parts-page {
        padding-bottom: 25px;
    }

    .parts-breadcrumb {
        text-align: right;
        margin-bottom: 15px;
        font-size: 13px;
        color: #777;
    }

    .parts-breadcrumb a {
        color: #555;
        text-decoration: none;
    }

    .parts-panel {
        background: #fff;
        border: 1px solid #e7e7e7;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,.035);
        overflow: hidden;
    }

    .parts-panel .panel-heading {
        background: #fff;
        border-bottom: 1px solid #ededed;
        padding: 18px 20px;
        min-height: 75px;
    }

    .parts-panel .panel-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin: 8px 0 0;
    }

    .parts-subtitle {
        font-size: 12px;
        color: #888;
        margin-top: 5px;
    }

    .parts-panel .panel-body {
        padding: 20px;
    }

    .btn-new-part {
        background: #444;
        color: #fff !important;
        border: 1px solid #444;
        border-radius: 4px;
        padding: 9px 16px;
        font-weight: 600;
        transition: .2s;
    }

    .btn-new-part:hover {
        background: #222;
        border-color: #222;
    }

    .parts-table {
        width: 100% !important;
        border: 1px solid #eaeaea;
        margin-bottom: 0 !important;
    }

    .parts-table thead th {
        background: #f5f6f7;
        color: #444;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 13px 10px !important;
        border-bottom: 1px solid #e5e5e5 !important;
        vertical-align: middle !important;
        white-space: nowrap;
    }

    .parts-table tbody td {
        padding: 12px 10px !important;
        vertical-align: middle !important;
        color: #555;
        font-size: 13px;
        border-color: #eee !important;
    }

    .parts-table tbody tr:hover {
        background: #fafafa;
    }

    .part-name {
        font-weight: 600;
        color: #333;
    }

    .part-quantity {
        font-weight: 600;
    }

    .part-actions {
        display: flex;
        align-items: center;
        gap: 5px;
        white-space: nowrap;
    }

    .part-actions .btn {
        min-width: 34px;
        padding: 6px 9px;
        border-radius: 4px;
    }

    .btn-view {
        background: #f2f3f4;
        color: #444;
        border: 1px solid #ddd;
    }

    .btn-view:hover {
        background: #e5e6e7;
        color: #222;
    }

    .btn-edit {
        background: #fff;
        color: #555;
        border: 1px solid #ccc;
    }

    .btn-edit:hover {
        background: #eee;
        color: #222;
    }

    .btn-delete {
        background: #fff;
        color: #b33a3a;
        border: 1px solid #d9baba;
    }

    .btn-delete:hover {
        background: #f8eeee;
        color: #922;
    }

    .parts-modal .modal-content {
        border: none;
        border-radius: 6px;
        box-shadow: 0 8px 30px rgba(0,0,0,.15);
        overflow: hidden;
    }

    .parts-modal .modal-header {
        background: #f7f7f7;
        border-bottom: 1px solid #e8e8e8;
        padding: 17px 20px;
    }

    .parts-modal .modal-title {
        font-size: 17px;
        font-weight: 600;
        color: #333;
    }

    .parts-modal .modal-body {
        padding: 22px;
    }

    .parts-modal .form-group {
        margin-bottom: 18px;
    }

    .parts-modal label {
        color: #444;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .parts-modal .form-control {
        height: 39px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: none;
        font-size: 13px;
    }

    .parts-modal .form-control:focus {
        border-color: #888;
        box-shadow: 0 0 0 2px rgba(0,0,0,.04);
    }

    .parts-modal textarea.form-control {
        height: auto;
    }

    .parts-modal .modal-footer {
        background: #fafafa;
        border-top: 1px solid #eee;
        padding: 14px 20px;
    }

    .required-mark {
        color: #b33a3a;
    }

    @media (max-width: 767px) {
        .parts-panel .panel-heading {
            padding: 15px;
        }

        .parts-panel .panel-body {
            padding: 12px;
        }

        .parts-panel .panel-title {
            font-size: 16px;
        }

        .parts-header-actions {
            float: none !important;
            margin-bottom: 12px;
        }

        .parts-header-actions .btn {
            width: 100%;
        }

        .parts-modal .modal-body {
            padding: 15px;
        }

        .parts-table {
            min-width: 850px;
        }
    }
</style>

<div class="parts-page">

    <div class="row">
        <div class="col-lg-12">
            <div class="parts-breadcrumb">
                <a href="#">Home</a>
                <i class="fa fa-angle-right"></i>
                Spare Parts
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">

            <div class="panel parts-panel">

                <div class="panel-heading clearfix">

                    <div class="parts-header-actions" style="float:right;">
                        <button type="button"
                                class="btn btn-new-part"
                                data-toggle="modal"
                                data-target=".bs-example-modal-lg">
                            <i class="fa fa-plus"></i> New Part
                        </button>
                    </div>

                    <h3 class="panel-title">
                        <i class="fa fa-cubes"></i>
                        All Spare Parts
                    </h3>

                    <div class="parts-subtitle">
                        Manage, view and maintain your spare parts inventory.
                    </div>

                </div>

                <div class="panel-body">

                    <div class="table-responsive">

                        <table id="datatable"
                               class="table table-striped table-bordered parts-table">

                            <thead>
                                <tr>
                                    <th width="45">#</th>
                                    <th>Item Name</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Part Number</th>
                                    <th>Qty</th>
                                    <th>Purchase Unit</th>
                                    <th width="145">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($products as $key => $product)

                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>
                                            <span class="part-name">
                                                {{ $product->item_name }}
                                            </span>
                                        </td>

                                        <td>{{ $product->title }}</td>

                                        <td>
                                            {{ strtoupper($product->model) }}
                                        </td>

                                        <td>
                                            {{ strtoupper($product->part_number) }}
                                        </td>

                                        <td>
                                            <span class="part-quantity">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ strtoupper($product->purchase_unit) }}
                                        </td>

                                        <td>
                                            <div class="part-actions">

                                                {{-- VIEW --}}
                                                <a href="{{ url('products/view-details/'.$product->id) }}"
                                                   class="btn btn-sm btn-view"
                                                   title="View Details">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                {{-- EDIT --}}
                                                <a href="{{ url('products/edit/'.$product->id) }}"
                                                   class="btn btn-sm btn-edit"
                                                   title="Edit Part">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                {{-- DELETE --}}
                                                @if(in_array(Auth::user()->role_id, [1, 2]))

                                                    <a href="{{ url('products/delete/'.$product->id) }}"
                                                       class="btn btn-sm btn-delete delete-part"
                                                       data-name="{{ $product->item_name }}"
                                                       data-url="{{ url('products/delete/'.$product->id) }}"
                                                       title="Delete Part">

                                                        <i class="fa fa-trash"></i>
                                                    </a>

                                                @endif

                                            </div>
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>


{{-- ================= ADD PART MODAL ================= --}}

<div class="modal fade bs-example-modal-lg parts-modal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <form action="{{ url('/products') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-hidden="true">
                        &times;
                    </button>

                    <h4 class="modal-title">
                        <i class="fa fa-plus-circle"></i>
                        Add Part or Item
                    </h4>

                </div>

                <input type="hidden"
                       name="branch_id"
                       value="{{ Auth::user()->branch_id }}">

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Item Name <span class="required-mark">*</span>
                                </label>

                                <select name="product_id"
                                        class="form-control select2"
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

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Make <span class="required-mark">*</span>
                                </label>

                                <select name="brand_id"
                                        class="form-control select2"
                                        id="brand_id"
                                        required>

                                    <option value="">Select Make</option>

                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Model <span class="required-mark">*</span>
                                </label>

                                <input type="text"
                                       name="model"
                                       id="model"
                                       class="form-control"
                                       placeholder="Enter model"
                                       required>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Part Number</label>

                                <input type="text"
                                       name="part_number"
                                       id="part_number"
                                       class="form-control"
                                       placeholder="Enter part number">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Purchasing Price <span class="required-mark">*</span>
                                </label>

                                <input type="number"
                                       step="any"
                                       min="0"
                                       name="purchasing_price"
                                       id="purchasing_price"
                                       class="form-control"
                                       placeholder="Purchasing price"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Selling Price <span class="required-mark">*</span>
                                </label>

                                <input type="number"
                                       step="any"
                                       min="0"
                                       name="selling_price"
                                       id="selling_price"
                                       class="form-control"
                                       placeholder="Selling price"
                                       required>
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Quantity <span class="required-mark">*</span>
                                </label>

                                <input type="number"
                                       step="any"
                                       min="0"
                                       name="quantity"
                                       id="quantity"
                                       class="form-control"
                                       placeholder="Enter quantity"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>
                                    Purchase Unit <span class="required-mark">*</span>
                                </label>

                                <input type="text"
                                       name="purchase_unit"
                                       id="purchase_unit"
                                       class="form-control"
                                       placeholder="e.g. PCS, Litre, Box"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Alert Quantity</label>

                                <input type="number"
                                       step="any"
                                       min="0"
                                       name="alert_qty"
                                       id="alert_qty"
                                       class="form-control"
                                       placeholder="Alert quantity">
                            </div>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>

                                <textarea name="description"
                                          id="description"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Enter part description"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i> Close
                    </button>

                    <button type="submit"
                            name="submit"
                            class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Part
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<script>
$(document).ready(function () {

    // CSRF configuration
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // Fetch assigned brands when item changes
    $('#product_id').on('change', function () {

        var product_id = $(this).val();

        $('#brand_id').html('<option value="">Loading...</option>');

        if (!product_id) {
            $('#brand_id').html('<option value="">Select Make</option>');
            return;
        }

        $.ajax({
            type: 'GET',
            url: '/product/fetch-assigned-items',
            data: {
                product_id: product_id
            },

            success: function (response) {
                $('#brand_id').html(response);

                // Refresh Select2 if available
                if ($.fn.select2) {
                    $('#brand_id').trigger('change.select2');
                }
            },

            error: function () {
                $('#brand_id').html('<option value="">Unable to load makes</option>');

                if (typeof swal === 'function') {
                    swal('Error', 'Unable to load assigned makes.', 'error');
                } else {
                    alert('Unable to load assigned makes.');
                }
            }
        });

    });


    // SweetAlert delete confirmation
    $(document).on('click', '.delete-part', function (e) {

        e.preventDefault();

        var deleteUrl = $(this).data('url');
        var partName = $(this).data('name');

        swal({
            title: 'Are you sure?',
            text: 'You are about to delete "' + partName + '". This action cannot be undone.',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#b33a3a',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {

            if (isConfirm) {
                window.location.href = deleteUrl;
            }

        });

    });

});
</script>

@endsection
