@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Management</h2>
            </div>
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Projects
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Projects</h5>
            <p class="card-text">Assign projects, change project leader</p>
            <a href="{{ route('projectadmin.index') }}" class="btn btn-outline-primary">Manage</a>
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Organisations and Users
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Organisations and Users</h5>
            <p class="card-text">Update organisation and user information (non spider staff), link users to organisations, add roles</p>
            <a href="{{ route('organisation.index') }}" class="btn btn-outline-primary">Manage</a>
            {{-- Old route }}
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Manage</a>
            {{ --}}

        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Roles
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Roles</h5>
            <p class="card-text">Role permissions, add or remove new permissions</p>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary">Manage</a>
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Settings
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Settings</h5>
            {{-- <p class="card-text">System settings, messages, </p> --}}
            <a href="{{ route('settings.index') }}" class="btn btn-outline-primary">Manage</a>
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Logs
        </div>
        <div class="card-body">
            <h5 class="card-title">View logs</h5>
            <a href="{{ route('logs') }}" class="btn btn-outline-primary">Logs</a>
        </div>
    </div>
    <div class="card my-3">
        <div class="card-header">
            Update system logo
        </div>
        <div class="card-body">
            <form action="{{ route('systemUploadLogo') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="logo">Select file:</label>
                    <input type="file" class="form-control-file" id="logo" name="logo">
                </div>
                <button type="submit" class="btn btn-primary">Upload & save</button>
            </form>
        </div>
    </div>
@endsection
