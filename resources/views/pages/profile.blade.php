
@extends('layouts.app_header')

@section('content')

<style>
/* =====================================================
   PROFESSIONAL EMPLOYEE PROFILE
===================================================== */

.employee-profile {
    padding-bottom: 30px;
    color: #526071;
    font-family: inherit;
}

.employee-profile .profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.employee-profile .profile-heading {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #344054;
}

.employee-profile .profile-subtitle {
    font-size: 13px;
    color: #8793a3;
    margin-top: 5px;
}

.employee-profile .profile-card {
    background: #fff;
    border: 1px solid #e8edf2;
    border-radius: 9px;
    box-shadow: 0 3px 15px rgba(20, 40, 70, .045);
    overflow: hidden;
}

.employee-profile .profile-sidebar {
    padding: 28px 20px;
    border-right: 1px solid #edf0f4;
    text-align: center;
    min-height: 480px;
}

.employee-profile .avatar-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 15px;
}

.employee-profile .profile-pic {
    width: 115px;
    height: 115px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #eef4fb;
    padding: 3px;
    background: #fff;
}

.employee-profile .avatar-edit {
    position: absolute;
    right: 2px;
    bottom: 5px;
    width: 33px;
    height: 33px;
    line-height: 33px;
    border-radius: 50%;
    background: #3979c9;
    color: #fff;
    cursor: pointer;
    border: 2px solid #fff;
    font-size: 13px;
}

.employee-profile .employee-name {
    color: #344054;
    font-size: 17px;
    font-weight: 600;
    margin: 7px 0 5px;
}

.employee-profile .employee-role {
    color: #8793a3;
    font-size: 12px;
}

.employee-profile .photo-btn {
    border: 1px solid #dce4ed;
    background: #fff;
    color: #526071;
    border-radius: 5px;
    padding: 8px 14px;
    font-size: 12px;
    margin-top: 12px;
}

.employee-profile .photo-btn:hover {
    background: #f5f8fc;
}

.employee-profile .profile-content {
    padding: 25px;
}

.employee-profile .nav-tabs {
    border-bottom: 1px solid #e8edf2;
    margin-bottom: 25px;
}

.employee-profile .nav-tabs > li > a {
    border: none !important;
    border-bottom: 2px solid transparent !important;
    border-radius: 0;
    padding: 12px 17px;
    color: #8793a3;
    font-size: 13px;
    font-weight: 500;
    background: transparent !important;
}

.employee-profile .nav-tabs > li.active > a,
.employee-profile .nav-tabs > li.active > a:hover,
.employee-profile .nav-tabs > li.active > a:focus {
    color: #3979c9;
    border-bottom: 2px solid #3979c9 !important;
    background: transparent !important;
}

.employee-profile .nav-tabs > li > a:hover {
    color: #3979c9;
}

.employee-profile .section-title {
    font-size: 15px;
    font-weight: 600;
    color: #344054;
    margin: 0 0 5px;
}

.employee-profile .section-description {
    color: #98a2b3;
    font-size: 12px;
    margin-bottom: 20px;
}

.employee-profile .details-table {
    width: 100%;
    border-collapse: collapse;
}

.employee-profile .details-table tr {
    border-bottom: 1px solid #f0f2f5;
}

.employee-profile .details-table td {
    padding: 15px 12px;
    font-size: 13px;
    vertical-align: middle;
}

.employee-profile .details-table td:first-child {
    width: 32%;
    color: #8793a3;
}

.employee-profile .details-table td:last-child {
    color: #344054;
    font-weight: 500;
}

.employee-profile .form-group {
    margin-bottom: 18px;
}

.employee-profile .control-label {
    color: #526071;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 7px;
}

.employee-profile .form-control {
    height: 42px;
    border: 1px solid #e0e6ed;
    border-radius: 5px;
    box-shadow: none;
    font-size: 13px;
}

.employee-profile .form-control:focus {
    border-color: #80aee3;
    box-shadow: 0 0 0 2px rgba(57,121,201,.07);
}

.employee-profile .btn-profile {
    background: #3979c9;
    color: #fff;
    border: 1px solid #3979c9;
    border-radius: 5px;
    padding: 10px 20px;
    font-size: 13px;
}

.employee-profile .btn-profile:hover {
    background: #2867b5;
    color: #fff;
}

.employee-profile .btn-outline {
    background: #fff;
    color: #526071;
    border: 1px solid #dce4ed;
    border-radius: 5px;
    padding: 9px 16px;
    font-size: 12px;
}

.employee-profile .btn-outline:hover {
    background: #f5f8fc;
}

.employee-profile .hidden-file {
    display: none !important;
}

/* Signature */

.employee-profile .signature-panel {
    max-width: 650px;
}

.employee-profile .signature-preview-box {
    background: #fafbfd;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    min-height: 210px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    position: relative;
}

.employee-profile .signature-preview-box img {
    max-width: 100%;
    max-height: 160px;
    object-fit: contain;
}

.employee-profile .signature-placeholder {
    color: #98a2b3;
    font-size: 13px;
}

.employee-profile .signature-placeholder i {
    font-size: 35px;
    margin-bottom: 12px;
    display: block;
    color: #b8c4d1;
}

.employee-profile .signature-actions {
    margin-top: 17px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.employee-profile .signature-info {
    background: #f5f8fc;
    border: 1px solid #e6edf5;
    border-radius: 6px;
    padding: 12px 15px;
    font-size: 12px;
    color: #68778a;
    margin-top: 17px;
}

.employee-profile .signature-status {
    font-size: 12px;
    color: #3979c9;
    margin-top: 10px;
}

.employee-profile .error-text {
    color: #dc3545;
    font-size: 12px;
    margin-top: 6px;
    display: block;
}

@media(max-width: 767px) {
    .employee-profile .profile-sidebar {
        min-height: auto;
        border-right: none;
        border-bottom: 1px solid #edf0f4;
        padding: 22px 15px;
    }

    .employee-profile .profile-content {
        padding: 15px;
    }

    .employee-profile .nav-tabs {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
    }

    .employee-profile .nav-tabs > li {
        float: none;
        flex: 0 0 auto;
    }

    .employee-profile .details-table td:first-child {
        width: 38%;
    }

    .employee-profile .details-table td {
        padding: 12px 6px;
        font-size: 12px;
    }

    .employee-profile .profile-heading {
        font-size: 19px;
    }
}
</style>

<div class="employee-profile">

    {{-- PAGE HEADER --}}
    <div class="profile-header">
        <div>
            <h3 class="profile-heading">My Profile</h3>
            <div class="profile-subtitle">
                Manage your personal information, security and signature
            </div>
        </div>

        <div>
            <span style="font-size:12px;color:#98a2b3;">
                Home / My Profile
            </span>
        </div>
    </div>

    <div class="profile-card">

        <div class="row" style="margin:0;">

            {{-- LEFT PROFILE SIDEBAR --}}
            <div class="col-md-3 profile-sidebar">

                <form id="formPost"
                      method="POST"
                      action="{{ url('/profile/update') }}"
                      enctype="multipart/form-data">

                    @csrf

                    <div class="avatar-wrapper">

                        @if(Auth::user()->profile_img)
                            <img
                                src="{{ url('/attachments/'.Auth::user()->profile_img) }}"
                                class="profile-pic"
                                id="profilePreview"
                                alt="Profile photo">
                        @else
                            <img
                                src="{{ asset('assets/images/users/profile.jpg') }}"
                                class="profile-pic"
                                id="profilePreview"
                                alt="Profile photo">
                        @endif

                        <label for="profile_img" class="avatar-edit">
                            <i class="fa fa-camera"></i>
                        </label>

                    </div>

                    <input type="file"
                           name="profile_img"
                           id="profile_img"
                           class="hidden-file"
                           accept="image/jpeg,image/png,image/jpg">

                    <input type="hidden"
                           name="user_id"
                           value="{{ Auth::user()->id }}">

                    <h4 class="employee-name">
                        {{ Auth::user()->fname }} {{ Auth::user()->lname }}
                    </h4>

                    <div class="employee-role">
                        {{ Auth::user()->is_admin == 1 ? 'Administrator' : 'Staff Member' }}
                    </div>

                    <button type="submit" class="photo-btn">
                        <i class="fa fa-upload"></i> Update Photo
                    </button>

                    @error('profile_img')
                        <span class="error-text">{{ $message }}</span>
                    @enderror

                </form>

            </div>

            {{-- RIGHT CONTENT --}}
            <div class="col-md-9 profile-content">

                {{-- NAVIGATION TABS --}}
                <ul class="nav nav-tabs" role="tablist">

                    <li class="active">
                        <a href="#home1" data-toggle="tab">
                            <i class="fa fa-user-o"></i> My Profile
                        </a>
                    </li>

                    <li>
                        <a href="#password1" data-toggle="tab">
                            <i class="fa fa-lock"></i> Change Password
                        </a>
                    </li>

                    <li>
                        <a href="#signature1" data-toggle="tab">
                            <i class="fa fa-pencil"></i> My Signature
                        </a>
                    </li>

                </ul>

                <div class="tab-content">

                    {{-- ==========================================
                         PROFILE TAB
                    =========================================== --}}
                    <div class="tab-pane fade in active" id="home1">

                        <h4 class="section-title">Personal Information</h4>

                        <p class="section-description">
                            Your registered account information.
                        </p>

                        <table class="details-table">

                            <tbody>

                                <tr>
                                    <td>Full Name</td>
                                    <td>
                                        {{ Auth::user()->fname }}
                                        {{ Auth::user()->lname }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Email Address</td>
                                    <td>{{ Auth::user()->email }}</td>
                                </tr>

                                <tr>
                                    <td>Mobile Number</td>
                                    <td>{{ Auth::user()->mobile }}</td>
                                </tr>

                                <tr>
                                    <td>Account Type</td>
                                    <td>
                                        {{ Auth::user()->is_admin == 1 ? 'Administrator' : 'Staff' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td>Account Status</td>
                                    <td>
                                        <span style="color:#39836a;">
                                            Active
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>


                    {{-- ==========================================
                         PASSWORD TAB
                    =========================================== --}}
                    <div class="tab-pane fade" id="password1">

                        <h4 class="section-title">Change Password</h4>

                        <p class="section-description">
                            Update your password to keep your account secure.
                        </p>

                        <form action="{{ url('/change_password') }}"
                              method="POST">

                            @csrf

                            <div class="row">

                                <div class="col-md-9">

                                    <div class="form-group">
                                        <label class="control-label">
                                            Current Password
                                        </label>

                                        <input type="password"
                                               name="current_password"
                                               class="form-control"
                                               placeholder="Enter current password"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">
                                            New Password
                                        </label>

                                        <input type="password"
                                               name="new_password"
                                               class="form-control"
                                               placeholder="Enter new password"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">
                                            Confirm New Password
                                        </label>

                                        <input type="password"
                                               name="confirm_password"
                                               class="form-control"
                                               placeholder="Confirm new password"
                                               required>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-profile">
                                        <i class="fa fa-save"></i>
                                        Update Password
                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>


                    {{-- ==========================================
                         SIGNATURE TAB
                    =========================================== --}}
                    <div class="tab-pane fade" id="signature1">

                        <div class="signature-panel">

                            <h4 class="section-title">
                                Employee Signature
                            </h4>

                            <p class="section-description">
                                Upload your signature for use in payroll,
                                employee documents and other company records.
                            </p>

                            <form id="signatureForm"
                                  action="{{ url('/profile/signature/update') }}"
                                  method="POST"
                                  enctype="multipart/form-data">

                                @csrf

                                <div class="form-group">

                                    <label class="control-label">
                                        Signature Preview
                                    </label>

                                    <div class="signature-preview-box">

                                        @if(Auth::user()->signature)

                                            <img
                                                src="{{ url('/'.Auth::user()->signature) }}"
                                                id="signaturePreview"
                                                alt="Employee signature">

                                            <div class="signature-placeholder"
                                                 id="signaturePlaceholder"
                                                 style="display:none;">
                                                <i class="fa fa-pencil"></i>
                                                No signature selected
                                            </div>

                                        @else

                                            <img
                                                src=""
                                                id="signaturePreview"
                                                alt="Signature preview"
                                                style="display:none;">

                                            <div class="signature-placeholder"
                                                 id="signaturePlaceholder">
                                                <i class="fa fa-pencil"></i>
                                                No signature uploaded yet
                                            </div>

                                        @endif

                                    </div>

                                    <div class="signature-status"
                                         id="signatureStatus">

                                        @if(Auth::user()->signature)
                                            <i class="fa fa-check-circle"></i>
                                            Saved signature
                                        @else
                                            Signature not uploaded
                                        @endif

                                    </div>

                                </div>

                                <input type="file"
                                       name="signature"
                                       id="signatureInput"
                                       class="hidden-file"
                                       accept=".png,.jpg,.jpeg,image/png,image/jpeg">

                                @error('signature')
                                    <span class="error-text">
                                        {{ $message }}
                                    </span>
                                @enderror

                                <div class="signature-actions">

                                    <button type="button"
                                            class="btn btn-outline"
                                            id="selectSignature">

                                        <i class="fa fa-upload"></i>

                                        {{ Auth::user()->signature ? 'Edit Signature' : 'Choose Signature' }}

                                    </button>

                                    <button type="submit"
                                            class="btn btn-profile"
                                            id="saveSignature"
                                            disabled>

                                        <i class="fa fa-save"></i>
                                        Save Signature

                                    </button>

                                    <button type="button"
                                            class="btn btn-outline"
                                            id="cancelSignature"
                                            style="display:none;">

                                        Cancel

                                    </button>

                                </div>

                                <div class="signature-info">
                                    <i class="fa fa-info-circle"></i>

                                    Upload a clear signature image.
                                    Accepted formats: PNG, JPG and JPEG.
                                    Maximum file size: 2MB.
                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE PHOTO PREVIEW
    |--------------------------------------------------------------------------
    */

    $('#profile_img').on('change', function () {

        const file = this.files[0];

        if (!file) return;

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Please select a JPG or PNG image.');
            this.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Profile image must not exceed 2MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            $('#profilePreview').attr('src', e.target.result);
        };

        reader.readAsDataURL(file);
    });


    /*
    |--------------------------------------------------------------------------
    | SIGNATURE PREVIEW AND EDIT
    |--------------------------------------------------------------------------
    */

    const signatureInput = $('#signatureInput');
    const signaturePreview = $('#signaturePreview');
    const signaturePlaceholder = $('#signaturePlaceholder');
    const saveButton = $('#saveSignature');
    const cancelButton = $('#cancelSignature');
    const signatureStatus = $('#signatureStatus');

    let originalSignature = signaturePreview.attr('src') || '';
    let originalHasSignature = originalSignature !== '';

    $('#selectSignature').on('click', function () {
        signatureInput.click();
    });

    signatureInput.on('change', function () {

        const file = this.files[0];

        if (!file) return;

        const allowedTypes = [
            'image/png',
            'image/jpeg'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Only PNG, JPG and JPEG files are allowed.');
            this.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Signature must not exceed 2MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {

            signaturePreview
                .attr('src', e.target.result)
                .show();

            signaturePlaceholder.hide();

            signatureStatus.html(
                '<i class="fa fa-eye"></i> New signature preview - not yet saved'
            );

            saveButton.prop('disabled', false);

            cancelButton.show();
        };

        reader.readAsDataURL(file);
    });


    /*
    |--------------------------------------------------------------------------
    | CANCEL SIGNATURE EDIT
    |--------------------------------------------------------------------------
    */

    cancelButton.on('click', function () {

        signatureInput.val('');

        if (originalHasSignature) {

            signaturePreview.attr('src', originalSignature).show();

            signaturePlaceholder.hide();

            signatureStatus.html(
                '<i class="fa fa-check-circle"></i> Saved signature'
            );

        } else {

            signaturePreview.attr('src', '').hide();

            signaturePlaceholder.show();

            signatureStatus.text('Signature not uploaded');

        }

        saveButton.prop('disabled', true);

        cancelButton.hide();
    });


    /*
    |--------------------------------------------------------------------------
    | SIGNATURE SUBMISSION
    |--------------------------------------------------------------------------
    */

    $('#signatureForm').on('submit', function (e) {

        if (!signatureInput[0].files.length) {

            e.preventDefault();

            alert('Please select a signature image before uploading.');

            return false;
        }

        saveButton.prop('disabled', true);

        saveButton.html(
            '<i class="fa fa-spinner fa-spin"></i> Uploading...'
        );

    });

});
</script>

@endsection
