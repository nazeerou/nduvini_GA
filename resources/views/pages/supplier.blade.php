
@extends('layouts.app_header')

@section('content')

<style>
    .suppliers-page {
        color: #475467;
    }

    .suppliers-page .panel {
        border: 1px solid #e8ebef;
        border-radius: 7px;
        box-shadow: 0 2px 10px rgba(0,0,0,.035);
        overflow: hidden;
    }

    .suppliers-page .panel-heading {
        padding: 18px 20px;
        background: #fff;
        border-bottom: 1px solid #edf0f2;
    }

    .suppliers-page .panel-title {
        font-size: 17px;
        font-weight: 600;
        color: #344054;
        margin-top: 12px;
    }

    .suppliers-page .panel-body {
        padding: 22px;
    }

    .suppliers-page .table {
        margin-bottom: 0;
    }

    .suppliers-page .table thead th {
        background: #f7f8fa;
        color: #526174;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 1px solid #e5e7eb;
        padding: 13px 12px;
        white-space: nowrap;
    }

    .suppliers-page .table tbody td {
        padding: 12px;
        vertical-align: middle;
        font-size: 13px;
        color: #475467;
    }

    .suppliers-page .table tbody tr:hover {
        background: #fafbfc;
    }

    .suppliers-page .btn {
        border-radius: 5px;
    }

    .suppliers-page .btn-new {
        padding: 9px 16px;
        font-weight: 600;
        margin-top: 2px;
    }

    .suppliers-page .action-btn {
        width: 32px;
        height: 30px;
        padding: 5px;
        margin-right: 3px;
    }

    .suppliers-page .step-indicator {
        margin-bottom: 15px;
    }

    .suppliers-page .step-indicator a {
        color: #84909d;
        font-size: 12px;
        text-decoration: none;
        margin-left: 8px;
    }

    /* Modal */
    .supplier-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,.12);
    }

    .supplier-modal .modal-header {
        padding: 20px 25px;
        background: #f8f9fa;
        border-bottom: 1px solid #e8ebef;
    }

    .supplier-modal .modal-title {
        font-size: 17px;
        font-weight: 600;
        color: #344054;
    }

    .supplier-modal .modal-header p {
        margin: 6px 0 0;
        color: #84909d;
        font-size: 12px;
    }

    .supplier-modal .modal-body {
        padding: 25px;
    }

    .supplier-modal .form-group {
        margin-bottom: 18px;
    }

    .supplier-modal .form-group label {
        display: block;
        color: #475467;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .supplier-modal .form-control {
        height: 40px;
        border: 1px solid #dfe3e8;
        border-radius: 5px;
        box-shadow: none;
        font-size: 13px;
        background: #fff;
    }

    .supplier-modal .form-control:focus {
        border-color: #8796a8;
        box-shadow: 0 0 0 2px rgba(82,103,127,.08);
    }

    .supplier-modal .modal-footer {
        padding: 15px 25px;
        background: #fafafa;
        border-top: 1px solid #ededed;
    }

    .supplier-modal .modal-footer .btn {
        min-width: 100px;
        padding: 9px 15px;
        font-weight: 600;
    }

    .supplier-section-title {
        font-size: 13px;
        font-weight: 600;
        color: #526174;
        padding-bottom: 10px;
        margin-bottom: 18px;
        border-bottom: 1px solid #ededed;
    }

    .supplier-section-title i {
        margin-right: 7px;
    }

    @media (max-width: 767px) {
        .suppliers-page .panel-body {
            padding: 12px;
        }

        .suppliers-page .btn-new {
            padding: 8px 10px;
            font-size: 12px;
        }

        .supplier-modal .modal-body {
            padding: 18px;
        }
    }
</style>

<div class="suppliers-page">

    {{-- Breadcrumb --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="step-indicator" style="float:right;">
                <a class="step completed" href="#">Home</a>
                <a class="step" href="#">Suppliers</a>
            </div>
        </div>
    </div>

    {{-- Supplier Listing --}}
    <div class="row">
        <div class="col-lg-12">

            <div class="panel panel-custom panel-border">

                <div class="panel-heading">

                    <div style="float:right;">
                        <button type="button"
                                class="btn btn-info btn-new"
                                data-toggle="modal"
                                data-target="#addSupplierModal">
                            <i class="fa fa-plus"></i> New Supplier
                        </button>
                    </div>

                    <h3 class="panel-title">
                        <i class="fa fa-truck"></i>
                        Supplier Lists
                    </h3>

                    <div style="clear:both;"></div>
                </div>

                <div class="panel-body">

                    {{-- Success Message --}}
                    @if(session('message'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <ul style="margin-bottom:0;padding-left:18px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="55">#</th>
                                    <th>Supplier Name</th>
                                    <th>Mobile Number</th>
                                    <th>Date Registered</th>
                                    <th width="110">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($suppliers as $key => $supplier)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>

                                        <td>
                                            <strong>
                                                {{ $supplier->supplier_name }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ $supplier->phone }}
                                        </td>

                                        <td>
                                            {{ $supplier->created_at }}
                                        </td>

                                        <td>
                                            <button type="button"
                                                    class="btn btn-default btn-sm action-btn edit-supplier"
                                                    title="Edit Supplier"
                                                    data-toggle="modal"
                                                    data-target="#editSupplierModal"
                                                    data-id="{{ $supplier->id }}"
                                                    data-name="{{ $supplier->supplier_name }}"
                                                    data-phone="{{ $supplier->phone }}">
                                                <i class="fa fa-pencil"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-default btn-sm action-btn delete-supplier"
                                                    title="Delete Supplier"
                                                    data-url="{{ url('suppliers/delete/'.$supplier->id) }}"
                                                    data-name="{{ $supplier->supplier_name }}">
                                                <i class="fa fa-trash text-danger"></i>
                                            </button>
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


{{-- ================= ADD SUPPLIER MODAL ================= --}}
<div class="modal fade" id="addSupplierModal" tabindex="-1"
     role="dialog" aria-labelledby="addSupplierModalLabel">

    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content supplier-modal">

            <form action="{{ url('/suppliers') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal">&times;</button>

                    <h4 class="modal-title" id="addSupplierModalLabel">
                        <i class="fa fa-plus-circle"></i>
                        Add New Supplier
                    </h4>

                    <p>Enter the supplier information below.</p>
                </div>

                <div class="modal-body">

                    <div class="supplier-section-title">
                        <i class="fa fa-user"></i>
                        Supplier Information
                    </div>

                    <div class="form-group">
                        <label>Supplier Name <span class="text-danger">*</span></label>

                        <input type="text"
                               name="supplier_name"
                               class="form-control"
                               placeholder="Enter supplier name"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               placeholder="Enter mobile number">
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
                        <i class="fa fa-save"></i> Save Supplier
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


{{-- ================= EDIT SUPPLIER MODAL ================= --}}
<div class="modal fade" id="editSupplierModal" tabindex="-1"
     role="dialog" aria-labelledby="editSupplierModalLabel">

    <div class="modal-dialog modal-md" role="document">

        <div class="modal-content supplier-modal">

            <form id="editSupplierForm"
                  method="POST"
                  action="{{ url('/suppliers/update') }}"
                  enctype="multipart/form-data">

                @csrf

                <input type="hidden" name="id" id="edit_supplier_id">

                <div class="modal-header">

                    <button type="button" class="close"
                            data-dismiss="modal">&times;</button>

                    <h4 class="modal-title" id="editSupplierModalLabel">
                        <i class="fa fa-pencil-square-o"></i>
                        Edit Supplier
                    </h4>

                    <p>Update the supplier information below.</p>

                </div>

                <div class="modal-body">

                    <div class="supplier-section-title">
                        <i class="fa fa-user"></i>
                        Supplier Information
                    </div>

                    <div class="form-group">
                        <label>Supplier Name <span class="text-danger">*</span></label>

                        <input type="text"
                               name="supplier_name"
                               id="edit_supplier_name"
                               class="form-control"
                               placeholder="Enter supplier name"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Mobile Number</label>

                        <input type="text"
                               name="phone"
                               id="edit_supplier_phone"
                               class="form-control"
                               placeholder="Enter mobile number">
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="updateSupplierBtn">
                        <i class="fa fa-save"></i> Update Supplier
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>


{{-- ================= JAVASCRIPT ================= --}}
<script>
$(document).ready(function () {

    // CSRF Setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Populate Edit Supplier Modal
    $(document).on('click', '.edit-supplier', function () {

        var button = $(this);

        $('#edit_supplier_id').val(button.attr('data-id'));
        $('#edit_supplier_name').val(button.attr('data-name'));
        $('#edit_supplier_phone').val(button.attr('data-phone'));

    });

    // Confirm Supplier Update
    $('#editSupplierForm').on('submit', function (e) {

        e.preventDefault();

        var form = this;
        var supplierName = $('#edit_supplier_name').val();

        swal({
            title: 'Update Supplier?',
            text: 'Are you sure you want to update ' + supplierName + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#52677f',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel',
            closeOnConfirm: false
        }, function (isConfirm) {

            if (isConfirm) {

                $('#updateSupplierBtn')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Updating...');

                form.submit();
            }

        });

    });

    // Confirm Supplier Deletion
    $(document).on('click', '.delete-supplier', function () {

        var deleteUrl = $(this).attr('data-url');
        var supplierName = $(this).attr('data-name');

        swal({
            title: 'Delete Supplier?',
            text: 'Are you sure you want to delete ' + supplierName + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d9534f',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            closeOnConfirm: true
        }, function (isConfirm) {

            if (isConfirm) {
                window.location.href = deleteUrl;
            }

        });

    });

    // Reset Edit Modal
    $('#editSupplierModal').on('hidden.bs.modal', function () {

        $('#editSupplierForm')[0].reset();

        $('#edit_supplier_id').val('');

        $('#updateSupplierBtn')
            .prop('disabled', false)
            .html('<i class="fa fa-save"></i> Update Supplier');

    });

});
</script>

@endsection
