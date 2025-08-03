@extends('layouts.app_header')

@section('content')
    <div class="container">
        <h2>Manage Roles and Permissions</h2>

        <!-- Manage Roles -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Roles</h3>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.createRole') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Role name" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="submit">Create Role</button>
                        </div>
                    </div>
                </form>
                <ul class="list-group">
                    @foreach ($roles as $role)
                        <li class="list-group-item">{{ $role->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Assign Roles and Permissions -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Assign Roles and Permissions</h3>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.assignRole') }}">
                    @csrf
                    <div class="form-group">
                        <label for="role">Select Role</label>
                        <select id="role" name="role" class="form-control">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-success">Assign Role</button>
                </form>

                <form method="post" action="{{ route('admin.assignPermission') }}">
                    @csrf
                    <div class="form-group mt-3">
                        <label for="permission">Select Permission</label>
                        <select id="permission" name="permission" class="form-control">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-success">Assign Permission</button>
                </form>
            </div>
        </div>

        <!-- Manage Permissions -->
        <div class="card">
            <div class="card-header">
                <h3>Permissions</h3>
            </div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.createPermission') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Permission name" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="submit">Create Permission</button>
                        </div>
                    </div>
                </form>
                <ul class="list-group">
                    @foreach ($permissions as $permission)
                        <li class="list-group-item">{{ $permission->name }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
