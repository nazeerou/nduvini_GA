@extends('layouts.app_header')

@section('content')

<style>
/* =====================================================
   CLIENT MANAGEMENT
===================================================== */

.clients-page {
    padding-bottom: 30px;
    color: #4b5563;
}

.clients-page .breadcrumb-area {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 18px;
}

.clients-page .panel {
    background: #fff;
    border: 1px solid #e8ebef;
    border-radius: 8px;
    box-shadow: 0 3px 14px rgba(0,0,0,.035);
    overflow: hidden;
}

.clients-page .panel-heading {
    background: #fff;
    border-bottom: 1px solid #edf0f3;
    padding: 20px 23px;
}

.clients-page .heading-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

.clients-page .heading-title {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
    color: #303946;
}

.clients-page .heading-subtitle {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #929aa5;
}

.clients-page .heading-icon {
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e5e9ed;
    border-radius: 7px;
    color: #586575;
    font-size: 17px;
    margin-right: 10px;
}

.clients-page .panel-body {
    padding: 22px;
}

.clients-page .add-client-btn {
    height: 38px;
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #d9e0e7;
    background: #f7f8fa;
    color: #394657;
    transition: all .2s ease;
}

.clients-page .add-client-btn:hover {
    background: #eceff2;
    border-color: #cbd3dc;
}

.clients-page .table-responsive {
    border: none;
    margin-bottom: 0;
}

.clients-page #datatable {
    width: 100% !important;
    margin-bottom: 0;
    border: 1px solid #e9edf1;
    border-collapse: separate;
    border-spacing: 0;
}

.clients-page #datatable thead th {
    background: #f5f7f9;
    color: #626d7a;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .4px;
    text-transform: uppercase;
    padding: 15px 12px;
    border: none;
    border-bottom: 1px solid #e7ebef;
    white-space: nowrap;
}

.clients-page #datatable tbody td {
    padding: 13px 12px;
    vertical-align: middle;
    font-size: 13px;
    color: #596270;
    border: none;
    border-bottom: 1px solid #edf0f3;
}

.clients-page #datatable tbody tr:last-child td {
    border-bottom: none;
}

.clients-page #datatable tbody tr:hover {
    background: #fafbfc;
}

.clients-page .client-name {
    color: #354252;
    font-weight: 600;
}

.clients-page .client-description {
    color: #788391;
    font-size: 12px;
    line-height: 1.7;
    width: 130px !important;
}

.clients-page .client-email {
    color: #56677a;
    text-decoration: none;
    width: 130px !important;

}

.clients-page .client-email:hover {
    text-decoration: underline;
}

.clients-page .client-date {
    white-space: nowrap;
    color: #77818d;
}

.clients-page .action-buttons {
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}


.clients-page .action-buttons .btn:hover {
    background: #f1f3f5;
    border-color: #cbd3dc;
}

.clients-page .action-buttons .btn-delete {
    color: #b94a48;
}

.clients-page .action-buttons .btn-delete:hover {
    background: #fff2f1;
    border-color: #ebcfcc;
}

/* =====================================================
   ADD CLIENT MODAL
===================================================== */

#addClientModal .modal-content {
    border: none;
    border-radius: 9px;
    overflow: hidden;
    box-shadow: 0 12px 40px rgba(0,0,0,.15);
}

#addClientModal .modal-header {
    padding: 19px 23px;
    background: #fff;
    border-bottom: 1px solid #edf0f3;
}

#addClientModal .modal-title {
    color: #303946;
    font-size: 16px;
    font-weight: 700;
}

#addClientModal .modal-title i {
    margin-right: 7px;
    color: #687789;
}

#addClientModal .modal-body {
    padding: 23px;
}

#addClientModal .modal-footer {
    padding: 15px 23px;
    background: #fafbfc;
    border-top: 1px solid #edf0f3;
}

#addClientModal .form-group {
    margin-bottom: 18px;
}

#addClientModal label {
    display: block;
    margin-bottom: 7px;
    color: #505b69;
    font-size: 12px;
    font-weight: 700;
}

#addClientModal .form-control {
    height: 40px;
    border: 1px solid #dfe4e9;
    border-radius: 5px;
    box-shadow: none;
    color: #414b58;
    font-size: 13px;
}

#addClientModal textarea.form-control {
    height: auto;
    min-height: 75px;
}

#addClientModal .form-control:focus {
    border-color: #9ba8b6;
    box-shadow: 0 0 0 2px rgba(110,125,145,.08);
}

#addClientModal .form-note {
    color: #929aa5;
    font-size: 11px;
    margin-top: 5px;
}

#addClientModal .btn {
    padding: 9px 17px;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
}

@media(max-width:767px) {
    .clients-page .panel-body {
        padding: 12px;
    }

    .clients-page .panel-heading {
        padding: 16px;
    }

    .clients-page .breadcrumb-area {
        justify-content: flex-start;
    }

    .clients-page .heading-content {
        align-items: flex-start;
    }

    #addClientModal .modal-dialog {
        margin: 12px;
    }

    #addClientModal .modal-body {
        padding: 17px;
    }
}


/* Edit Client Modal */
#editClientModal .clients-modal {
    border: 0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,.12);
}

#editClientModal .clients-modal-header {
    padding: 22px 25px;
    background: #f8f9fa;
    border-bottom: 1px solid #e8e8e8;
}

#editClientModal .clients-modal-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #344054;
}

#editClientModal .clients-modal-header p {
    margin: 7px 0 0;
    font-size: 12px;
    color: #84909d;
}

#editClientModal .modal-body {
    padding: 25px;
}

#editClientModal .clients-section-title {
    font-size: 13px;
    font-weight: 600;
    color: #526174;
    padding-bottom: 10px;
    margin: 5px 0 18px;
    border-bottom: 1px solid #ededed;
}

#editClientModal .clients-section-title i {
    margin-right: 7px;
}

#editClientModal .form-group {
    margin-bottom: 18px;
}

#editClientModal .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #475467;
    margin-bottom: 7px;
}

#editClientModal .form-control {
    height: 40px;
    border: 1px solid #dfe3e8;
    border-radius: 5px;
    box-shadow: none;
    background: #fff;
    font-size: 13px;
}

#editClientModal .form-control:focus {
    border-color: #8796a8;
    box-shadow: 0 0 0 2px rgba(82,103,127,.08);
}

#editClientModal .clients-modal-footer {
    padding: 15px 25px;
    background: #fafafa;
    border-top: 1px solid #ededed;
}

#editClientModal .clients-modal-footer .btn {
    min-width: 115px;
    border-radius: 5px;
    padding: 9px 15px;
    font-weight: 600;
}

#editClientModal .close {
    font-size: 25px;
    opacity: .5;
}

</style>


<div class="clients-page">

    <!-- BREADCRUMB -->
    <div class="row">
        <div class="col-md-12">
            <div class="breadcrumb-area">
                <div class="step-indicator">
                    <a class="step completed" href="{{ url('home') }}">Home</a>
                    <a class="step" href="#">Clients</a>
                </div>
            </div>
        </div>
    </div>


    <!-- CLIENTS PANEL -->
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-custom panel-border">

                <div class="panel-heading">

                    <div class="heading-content">

                        <div style="display:flex;align-items:center;">

                            <div class="heading-icon">
                                <i class="fa fa-users"></i>
                            </div>

                            <div>
                                <h3 class="heading-title">Clients List</h3>
                                <span class="heading-subtitle">
                                    Manage your registered clients and their details
                                </span>
                            </div>

                        </div>

                        <button type="button"
                                class="btn add-client-btn"
                                data-toggle="modal"
                                data-target="#addClientModal">
                            <i class="fa fa-plus"></i>
                            &nbsp; Add New Client
                        </button>

                    </div>

                </div>


                <div class="panel-body">

                    <div class="table-responsive">

                        <table id="datatable"
                               class="table table-striped table-bordered">

                            <thead>
                                <tr>
                                    <th width="50">#</th>
                                    <th>Client Name</th>
                                    <th>Description</th>
                                    <th>Contact</th>
                                    <th>Address</th>
                                    <th>Date Created</th>
                                    <th width="110">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($clients as $key => $client)

                                <tr>

                                    <td>{{ $key + 1 }}</td>

                                    <td>
                                        <span class="client-name">
                                            {{ $client->client_name }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="client-description">
                                            <strong>TIN:</strong>
                                            {{ $client->tin ?: 'N/A' }}
                                            <br>
                                            <strong>VRN:</strong>
                                            {{ $client->vrn ?: 'N/A' }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($client->email)
                                           Email : <a class="client-email"
                                               href="mailto:{{ $client->email }}">
                                                {{ $client->email }}
                                            </a> <br>
                                            Mobile : <span class="text-muted">{{ $client->phone }} </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $client->address ?: 'N/A' }}
                                        @if($client->place)
                                            - {{ $client->place }}
                                        @endif
                                    </td>

                                    <td>
                                        <span class="client-date">
                                            {{ $client->created_date }}
                                        </span>
                                    </td>

                                    <td>

                                        <div class="action-buttons">

                                            <!-- EDIT -->
                                        <button type="button"
                                                class="btn btn-default btn-sm edit-client"
                                                data-toggle="modal"
                                                data-target="#editClientModal"
                                                data-id="{{ $client->id }}"
                                                data-name="{{ $client->client_name }}"
                                                data-abbreviation="{{ $client->abbreviation }}"
                                                data-address="{{ $client->address }}"
                                                data-place="{{ $client->place }}"
                                                data-phone="{{ $client->phone }}"
                                                data-email="{{ $client->email }}"
                                                data-tin="{{ $client->tin }}"
                                                data-vrn="{{ $client->vrn }}">
                                            <i class="fa fa-pencil"></i> Edit
                                        </button>
                                                                                    <!-- DELETE -->
                                            @if(in_array(Auth::user()->role_id, [1, 2]))

                                            <button type="button"
                                                    class="btn btn-delete delete-client"
                                                    title="Delete Client"
                                                    data-url="{{ url('clients/delete/'.$client->id) }}"
                                                    data-name="{{ $client->client_name }}">
                                                <i class="fa fa-trash"></i>
                                            </button>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="7"
                                        class="text-center"
                                        style="padding:35px;color:#929aa5;">

                                        <i class="fa fa-users"
                                           style="font-size:28px;display:block;margin-bottom:10px;"></i>

                                        No clients found.

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
     ADD CLIENT MODAL
===================================================== -->

<div class="modal fade"
     id="addClientModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="addClientTitle"
     aria-hidden="true">

    <div class="modal-dialog modal-md">

        <div class="modal-content">

            <form action="{{ url('/clients/add') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-header">

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h4 class="modal-title" id="addClientTitle">
                        <i class="fa fa-user-plus"></i>
                        Add New Client
                    </h4>

                </div>


                <div class="modal-body">

                    <div class="form-group">
                        <label>Client Name <span class="text-danger">*</span></label>

                        <input type="text"
                               name="client_name"
                               id="client_name"
                               class="form-control"
                               placeholder="Enter client name"
                               required>
                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Abbreviation</label>

                                <input type="text"
                                       name="abbreviation"
                                       id="abbreviation"
                                       class="form-control"
                                       placeholder="E.g. TRA">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>

                                <input type="text"
                                       name="phone"
                                       id="phone"
                                       class="form-control"
                                       placeholder="Mobile number">
                            </div>
                        </div>

                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>

                                <input type="text"
                                       name="address"
                                       id="address"
                                       class="form-control"
                                       placeholder="E.g. P.O. Box 290">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Place</label>

                                <input type="text"
                                       name="place"
                                       id="place"
                                       class="form-control"
                                       placeholder="E.g. Dodoma">
                            </div>
                        </div>

                    </div>


                    <div class="form-group">
                        <label>Email Address</label>

                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control"
                               placeholder="Enter email address">
                    </div>


                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TIN Number</label>

                                <input type="text"
                                       name="tin"
                                       id="tin"
                                       class="form-control"
                                       placeholder="Enter TIN number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>VRN Number</label>

                                <input type="text"
                                       name="vrn"
                                       id="vrn"
                                       class="form-control"
                                       placeholder="Enter VRN number">
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
                            name="submit"
                            class="btn btn-primary"
                            id="saveClientBtn">
                        <i class="fa fa-save"></i>
                        Save Client
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ================= EDIT CLIENT MODAL ================= --}}
<div class="modal fade" id="editClientModal" tabindex="-1"
     role="dialog" aria-labelledby="editClientModalLabel">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content clients-modal">

            <form id="editClientForm"
                  method="POST"
                  action="{{ url('clients/update') }}"
                  enctype="multipart/form-data">

                @csrf

                <div class="modal-header clients-modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                    <h4 class="modal-title" id="editClientModalLabel">
                        <i class="fa fa-pencil-square-o"></i>
                        Edit Client Information
                    </h4>

                    <p>Update the client's details below.</p>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="id" id="edit_client_id">

                    <div class="clients-section-title">
                        <i class="fa fa-user"></i>
                        Client Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client Name</label>
                                <input type="text"
                                       name="client_name"
                                       id="edit_client_name"
                                       class="form-control"
                                       placeholder="Enter client name"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Abbreviation</label>
                                <input type="text"
                                       name="abbreviation"
                                       id="edit_abbreviation"
                                       class="form-control"
                                       placeholder="Enter abbreviation">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <input type="text"
                                       name="address"
                                       id="edit_address"
                                       class="form-control"
                                       placeholder="Enter address">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Place</label>
                                <input type="text"
                                       name="place"
                                       id="edit_place"
                                       class="form-control"
                                       placeholder="Enter place">
                            </div>
                        </div>
                    </div>

                    <div class="clients-section-title">
                        <i class="fa fa-address-card-o"></i>
                        Contact Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text"
                                       name="phone"
                                       id="edit_phone"
                                       class="form-control"
                                       placeholder="Enter phone number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email"
                                       name="email"
                                       id="edit_email"
                                       class="form-control"
                                       placeholder="Enter email address">
                            </div>
                        </div>
                    </div>

                    <div class="clients-section-title">
                        <i class="fa fa-file-text-o"></i>
                        Tax Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>TIN Number</label>
                                <input type="text"
                                       name="tin"
                                       id="edit_tin"
                                       class="form-control"
                                       placeholder="Enter TIN number">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>VRN Number</label>
                                <input type="text"
                                       name="vrn"
                                       id="edit_vrn"
                                       class="form-control"
                                       placeholder="Enter VRN number">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer clients-modal-footer">
                    <button type="button"
                            class="btn btn-default"
                            data-dismiss="modal">
                        <i class="fa fa-times"></i> Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="updateClientBtn">
                        <i class="fa fa-save"></i> Update Client
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- =====================================================
     JAVASCRIPT - SWEETALERT CONFIRMATIONS
===================================================== -->

<script>
$(document).ready(function () {

    // CSRF SETUP
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // ==========================================
    // EDIT CLIENT — populate modal on click (no confirmation)
    // Modal opens via data-toggle="modal" data-target="#editClientModal"
    // ==========================================
    $(document).on('click', '.edit-client', function () {

        var $btn = $(this);

        $('#edit_client_id').val($btn.data('id'));
        $('#edit_client_name').val($btn.data('name'));
        $('#edit_abbreviation').val($btn.data('abbreviation') || '');
        $('#edit_address').val($btn.data('address') || '');
        $('#edit_place').val($btn.data('place') || '');
        $('#edit_phone').val($btn.data('phone') || '');
        $('#edit_email').val($btn.data('email') || '');
        $('#edit_tin').val($btn.data('tin') || '');
        $('#edit_vrn').val($btn.data('vrn') || '');

    });


    // ==========================================
    // DELETE CLIENT CONFIRMATION
    // ==========================================

    $(document).on('click', '.delete-client', function (e) {

        e.preventDefault();

        var deleteUrl = $(this).data('url');
        var clientName = $(this).data('name');

        if (!deleteUrl) {
            swal('Error', 'Client delete URL is missing.', 'error');
            return;
        }

        swal({

            title: 'Delete Client?',
            text: 'Are you sure you want to delete ' +
                  clientName + '? This action cannot be undone.',

            type: 'warning',

            showCancelButton: true,
            confirmButtonColor: '#b94a48',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',

            closeOnConfirm: true

        }, function (isConfirm) {

            if (isConfirm) {
                window.location.href = deleteUrl;
            }

        });

    });


    // ==========================================
    // ADD CLIENT FORM LOADING
    // ==========================================

    $('#addClientModal form').on('submit', function () {

        if (this.checkValidity && !this.checkValidity()) {
            return;
        }

        $('#saveClientBtn')
            .prop('disabled', true)
            .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    });


    // RESET ADD FORM ON CLOSE
    $('#addClientModal').on('hidden.bs.modal', function () {

        $(this).find('form')[0].reset();

        $('#saveClientBtn')
            .prop('disabled', false)
            .html('<i class="fa fa-save"></i> Save Client');

    });

});
</script>

@endsection