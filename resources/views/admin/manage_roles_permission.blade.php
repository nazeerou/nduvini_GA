@extends('layouts.app_header')

@section('content')
    <div class="container">
        <h2>Manage Roles and Permissions</h2>

        <div>
            <h3>Roles</h3>
            <ul>
                @foreach ($roles as $role)
                    <li>{{ $role->name }}</li>
                @endforeach
            </ul>
        </div>

        <div>
            <h3>Permissions</h3>
            <ul>
                @foreach ($permissions as $permission)
                    <li>{{ $permission->name }}</li>
                @endforeach
            </ul>
        </div>

        <h3>Assign Permissions to Users</h3>
        <form method="post" action="{{ route('assignPermission') }}">
            @csrf
            <label for="user">Select User:</label>
            <select name="user" id="user">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <label for="permission">Select Permission:</label>
            <select name="permission" id="permission">
                @foreach ($permissions as $permission)
                    <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                @endforeach
            </select>

            <button type="submit">Assign Permission</button>
        </form>
    </div>
@endsection
