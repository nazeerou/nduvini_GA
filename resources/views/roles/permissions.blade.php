
@extends('layouts.app_header')

@section('content')

<style>
    .permission-panel {
        border: 1px solid #e5e9ef;
        border-top: 2px solid #5b9bd5;
        border-radius: 4px;
        background: #fff;
        margin-top: 10px;
    }

    .permission-title {
        padding: 14px 15px 10px;
        color: #475569;
        font-size: 13px;
        border-bottom: 1px solid #e5e9ef;
    }

    .permission-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
        font-family: 'Raleway', sans-serif;
        font-size: 11px;
    }

    .permission-table thead th {
        background: #add;
        color: #475569;
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 500;
        padding: 9px 7px;
        border: 1px solid #d5e1e8;
        text-align: left;
    }

    .permission-table tbody td {
        padding: 8px 7px;
        border: 1px solid #e5eaf0;
        color: #475569;
        vertical-align: top;
    }

    .permission-table tbody tr:hover {
        background: #f9fcff;
    }

    .permission-module {
        font-weight: 500;
        color: #475569;
        white-space: nowrap;
    }

    .permission-item {
        display: block;
        margin-bottom: 7px;
        font-weight: 400;
        cursor: pointer;
        color: #475569;
    }

    .permission-item:last-child {
        margin-bottom: 0;
    }

    .permission-item input,
    .module-checkbox,
    .select-all-checkbox {
        margin-right: 4px;
        vertical-align: middle;
        cursor: pointer;
    }

    .options-cell {
        white-space: normal;
    }

    .option-item {
        display: inline-block;
        margin: 0 9px 5px 0;
        cursor: pointer;
        font-weight: 400;
        color: #64748b;
    }

    .option-item input {
        margin: 0 3px 0 0;
        vertical-align: middle;
        cursor: pointer;
    }

    .permission-footer {
        padding: 12px 15px;
        border-top: 1px solid #e5eaf0;
        text-align: right;
        background: #fafafa;
    }

    .permission-footer .btn {
        padding: 7px 20px;
        font-size: 12px;
    }

    .permission-actions {
        padding: 9px 15px;
        border-bottom: 1px solid #e5eaf0;
        font-size: 11px;
        color: #64748b;
    }

    .permission-actions label {
        cursor: pointer;
        margin: 0;
    }

    .permission-actions input {
        vertical-align: middle;
        margin-right: 4px;
    }

    .permission-empty {
        color: #b0b8c2;
        font-size: 11px;
    }

    @media(max-width: 767px) {
        .permission-table {
            min-width: 650px;
        }
    }
</style>


{{-- PAGE BREADCRUMB --}}
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float:right">
            <a class="step completed" href="{{ url('home') }}">Home</a>
            <a class="step" href="#">USER PERMISSION</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-custom panel-border permission-panel">

            <div class="panel-body">

                {{-- TITLE --}}
                <div class="permission-title">
                    Manage Permissions for Role :
                    <strong>{{ strtoupper($role->name) }}</strong>
                </div>


                {{-- PERMISSION FORM --}}
                <form action="{{ route('roles.updatePermissions', $role->id) }}"
                      method="POST"
                      id="permissionForm">

                    @csrf


                    {{-- SELECT ALL --}}
                    <div class="permission-actions">

                        <label>
                            <input type="checkbox" id="selectAllModules">
                            Select All Modules
                        </label>

                        <span style="float:right">
                            Select permissions for each module
                        </span>

                    </div>


                    <div class="table-responsive">

                        <table class="permission-table">

                            <thead>
                                <tr>
                                    <th style="width:24%">MODULE</th>
                                    <th style="width:38%">PERMISSIONS</th>
                                    <th style="width:38%">CRUD OPTIONS</th>
                                </tr>
                            </thead>


                            <tbody>

                                @foreach ($modules as $module)

                                    @php

                                        /*
                                        |--------------------------------------------------------------------------
                                        | MODULE PERMISSIONS
                                        |--------------------------------------------------------------------------
                                        */

                                        $modulePermissions = collect($module->permissions);


                                        /*
                                        |--------------------------------------------------------------------------
                                        | NORMALIZE MODULE NAME
                                        |--------------------------------------------------------------------------
                                        */

                                        $moduleKey = strtolower(trim($module->name));

                                        $moduleKey = preg_replace(
                                            '/[^a-z0-9]+/',
                                            '-',
                                            $moduleKey
                                        );

                                        $moduleKey = trim($moduleKey, '-');


                                        /*
                                        |--------------------------------------------------------------------------
                                        | MODULE NAME MAPPING
                                        |--------------------------------------------------------------------------
                                        | Add mappings here if your database module names
                                        | differ from the permission suffix.
                                        */

                                        $moduleKey = match ($moduleKey) {

                                            'invoice' => 'invoices',
                                            'estimate' => 'estimates',

                                            default => $moduleKey

                                        };


                                        /*
                                        |--------------------------------------------------------------------------
                                        | CRUD ACTIONS
                                        |--------------------------------------------------------------------------
                                        */

                                        $crudActions = [
                                            'create',
                                            'view',
                                            'edit',
                                            'delete'
                                        ];


                                        /*
                                        |--------------------------------------------------------------------------
                                        | FIND CRUD PERMISSIONS
                                        |--------------------------------------------------------------------------
                                        | Example:
                                        | create-invoices
                                        | view-invoices
                                        | edit-invoices
                                        | delete-invoices
                                        */

                                        $crudPermissions = collect($crudActions)
                                            ->mapWithKeys(function ($action) use (
                                                $modulePermissions,
                                                $moduleKey
                                            ) {

                                                $permissionName = $action . '-' . $moduleKey;

                                                $permission = $modulePermissions->first(
                                                    function ($p) use ($permissionName) {

                                                        return strtolower(trim($p->name))
                                                            === $permissionName;

                                                    }
                                                );

                                                return [
                                                    $permissionName => $permission
                                                ];

                                            });


                                        /*
                                        |--------------------------------------------------------------------------
                                        | REMOVE CRUD PERMISSIONS FROM REGULAR LIST
                                        |--------------------------------------------------------------------------
                                        */

                                        $crudIds = $crudPermissions
                                            ->filter()
                                            ->pluck('id')
                                            ->map(fn($id) => (int) $id)
                                            ->toArray();


                                        $regularPermissions = $modulePermissions->reject(
                                            function ($permission) use ($crudIds) {

                                                return in_array(
                                                    (int) $permission->id,
                                                    $crudIds
                                                );

                                            }
                                        );


                                        /*
                                        |--------------------------------------------------------------------------
                                        | MODULE PERMISSION IDS
                                        |--------------------------------------------------------------------------
                                        */

                                        $modulePermissionIds = $modulePermissions
                                            ->pluck('id')
                                            ->map(fn($id) => (int) $id)
                                            ->toArray();


                                        /*
                                        |--------------------------------------------------------------------------
                                        | CHECK MODULE ASSIGNMENT
                                        |--------------------------------------------------------------------------
                                        */

                                        $moduleChecked = collect($modulePermissionIds)
                                            ->intersect($assigned_permissions)
                                            ->isNotEmpty();

                                    @endphp


                                    <tr>

                                        {{-- MODULE --}}
                                        <td>

                                            <label class="permission-module">

                                                <input
                                                    type="checkbox"
                                                    class="module-checkbox"
                                                    data-module="{{ $module->id }}"
                                                >

                                                {{ ucfirst($module->name) }}

                                            </label>

                                        </td>


                                        {{-- REGULAR DATABASE PERMISSIONS --}}
                                        <td>

                                            @forelse($regularPermissions as $permission)

                                                <label class="permission-item">

                                                    <input
                                                        type="checkbox"
                                                        class="permission-checkbox permission-module-{{ $module->id }}"
                                                        name="permissions[]"
                                                        value="{{ $permission->id }}"
                                                        data-module="{{ $module->id }}"
                                                        {{ in_array((int) $permission->id, $assigned_permissions) ? 'checked' : '' }}
                                                    >

                                                    {{ ucfirst(str_replace(
                                                        ['-', '_'],
                                                        ' ',
                                                        $permission->name
                                                    )) }}

                                                </label>

                                            @empty

                                                <span class="permission-empty">
                                                    No additional permissions
                                                </span>

                                            @endforelse

                                        </td>


                                        {{-- CRUD OPTIONS --}}
                                        <td class="options-cell">

                                            @foreach($crudActions as $action)

                                                @php

                                                    $permissionName = $action . '-' . $moduleKey;

                                                    $permission = $crudPermissions[$permissionName] ?? null;

                                                @endphp


                                                @if($permission)

                                                    <label class="option-item">

                                                        <input
                                                            type="checkbox"
                                                            class="permission-checkbox permission-module-{{ $module->id }}"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            data-module="{{ $module->id }}"
                                                            {{ in_array((int) $permission->id, $assigned_permissions) ? 'checked' : '' }}
                                                        >

                                                        {{ ucfirst($action) }}

                                                    </label>

                                                @endif

                                            @endforeach


                                            @if($crudPermissions->filter()->isEmpty())

                                                <span class="permission-empty">
                                                    No CRUD permissions
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- FOOTER --}}
                    <div class="permission-footer">

                        <button type="submit" class="btn btn-success btn-rounded">

                            <i class="fa fa-save"></i>
                            Update Permissions

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>


{{-- JAVASCRIPT --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');

    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

    const selectAll = document.getElementById('selectAllModules');


    /*
    |--------------------------------------------------------------------------
    | UPDATE MODULE CHECKBOX
    |--------------------------------------------------------------------------
    */

    function updateModuleCheckbox(moduleId) {

        const module = document.querySelector(
            '.module-checkbox[data-module="' + moduleId + '"]'
        );

        const permissions = document.querySelectorAll(
            '.permission-module-' + moduleId
        );

        const checkedCount = [...permissions].filter(
            checkbox => checkbox.checked
        ).length;

        module.checked = permissions.length > 0 &&
                         checkedCount === permissions.length;

        module.indeterminate = checkedCount > 0 &&
                               checkedCount < permissions.length;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECT ALL CHECKBOX
    |--------------------------------------------------------------------------
    */

    function updateSelectAll() {

        const checkedModules = [...moduleCheckboxes].filter(
            checkbox => checkbox.checked && !checkbox.indeterminate
        ).length;

        selectAll.checked = moduleCheckboxes.length > 0 &&
                            checkedModules === moduleCheckboxes.length;

        selectAll.indeterminate = checkedModules > 0 &&
                                  checkedModules < moduleCheckboxes.length;

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT / DESELECT MODULE PERMISSIONS
    |--------------------------------------------------------------------------
    */

    moduleCheckboxes.forEach(module => {

        module.addEventListener('change', function () {

            const moduleId = this.dataset.module;

            const permissions = document.querySelectorAll(
                '.permission-module-' + moduleId
            );

            permissions.forEach(permission => {

                permission.checked = this.checked;

            });

            this.indeterminate = false;

            updateSelectAll();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL PERMISSION SELECTION
    |--------------------------------------------------------------------------
    */

    permissionCheckboxes.forEach(permission => {

        permission.addEventListener('change', function () {

            updateModuleCheckbox(this.dataset.module);

            updateSelectAll();

        });

    });


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL MODULES
    |--------------------------------------------------------------------------
    */

    selectAll.addEventListener('change', function () {

        const checked = this.checked;

        moduleCheckboxes.forEach(module => {

            module.checked = checked;
            module.indeterminate = false;

            const permissions = document.querySelectorAll(
                '.permission-module-' + module.dataset.module
            );

            permissions.forEach(permission => {

                permission.checked = checked;

            });

        });

        updateSelectAll();

    });


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    moduleCheckboxes.forEach(module => {

        updateModuleCheckbox(module.dataset.module);

    });

    updateSelectAll();

});

</script>

@endsection
