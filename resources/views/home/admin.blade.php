@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Management</h2>
            </div>
            <br>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Users
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Users</h5>
            <p class="card-text">Update user info, assign projects, add roles</p>
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary">Manage</a>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            Roles
        </div>
        <div class="card-body">
            <h5 class="card-title">Manage Roles</h5>
            <p class="card-text">Role permissions, add or remove</p>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-primary">Manage</a>
        </div>
    </div>

@endsection
