@extends('layouts.app_header')
@section('content')
<style>
   td th {
  /* font-weight: 400; */
  background: #add;
  color: #000;
  text-transform: uppercase;
  font-size: 1em;
  font-family: 'Raleway', sans-serif;
  border: 1px solid #add;
 }
 th {
  /* font-weight: 400; */
  background: #add;
  color: #000;
  text-transform: uppercase;
  font-size: 1em;
  font-family: 'Raleway', sans-serif;
  border: 1px solid #add;
 }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="step-indicator" style="float: right">
            <a class="step completed" href="#">Home</a> 
            <a class="step" href="#">USER PERMISSION  </a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-custom panel-border">
            <div class="panel-heading">
                 <!-- <h4 class="panel-title1"> USER ROLES AND PERMISSIONS  </h4> -->
            </div>
<div class="panel-body"> 
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h4 class="mb-0">Manage Permissions for Role : <strong>{{ $role->name }}</strong></h4>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.updatePermissions', $role->id) }}" method="POST">
                    @csrf
                    @method('POST')

                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="350px">Module</th>
                                <th>Permissions</th>

								<th>Options</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modules as $module)
                                @php
                                    $moduleChecked = collect($module->permissions)->pluck('id')->intersect($assigned_permissions)->isNotEmpty();
                                @endphp
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                class="form-check-input module-checkbox"
                                                id="module-{{ $module->id }}"
                                                name="modules[]"
                                                value="{{ $module->id }}"
                                                onchange="toggleModulePermissions('{{ $module->id }}')"
                                                {{ $moduleChecked ? 'checked' : '' }}>
                                            <label class="form-check-label" for="module-{{ $module->id }}">
                                                {{ ucfirst($module->name) }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @foreach ($module->permissions as $permission)
                                            <div class="form-check form-check-inline mb-1">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input permission-checkbox permission-{{ $module->id }}"
                                                    id="permission-{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, $assigned_permissions) ? 'checked' : '' }}
                                                    onchange="updateModuleCheckbox('{{ $module->id }}')">
                                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                    {{ ucfirst(str_replace('-', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </td>
									<td> 
										<input type="checkbox" class="form-check-input" value="View"> View
										<input type="checkbox" class="form-check-input" value="View"> Edit
										<input type="checkbox" class="form-check-input" value="View"> Delete
								       </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success btn-rounded">
                            Update Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script>
   // Function to toggle all permissions when module is selected
function toggleModulePermissions(moduleId) {
    const moduleCheckbox = document.getElementById(`module-${moduleId}`);
    const permissionsCheckboxes = document.querySelectorAll(`.permission-${moduleId}`);

    permissionsCheckboxes.forEach(checkbox => {
        checkbox.checked = moduleCheckbox.checked;
    });
}

// Function to check module when any permission is selected
function updateModuleCheckbox(moduleId) {
    const moduleCheckbox = document.getElementById(`module-${moduleId}`);
    const permissionsCheckboxes = document.querySelectorAll(`.permission-${moduleId}`);

    // If at least one permission is checked, check the module
    const anyChecked = [...permissionsCheckboxes].some(checkbox => checkbox.checked);
    moduleCheckbox.checked = anyChecked;
}

// Ensure modules are checked when loading the page (in case of pre-selected permissions)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
        let moduleId = moduleCheckbox.id.replace('module-', '');
        updateModuleCheckbox(moduleId);
    });
});

</script>
@endsection