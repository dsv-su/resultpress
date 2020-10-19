@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Create New User</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('users.index') }}"> Back</a>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            There were some problems with your input.
            <br>
            <br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach</ul></div>
    @endif
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                <input class="form-control"  name="name" type="text" placeholder="Name">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                <input class="form-control"  name="email" type="email" placeholder="Email">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Password:</strong>
                <input class="form-control"  name="password" type="password" placeholder="Password">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12"><div class="form-group">
                <strong>Confirm Password:</strong>
                <input class="form-control"  name="confirm-password" type="password" placeholder="Confirm Password">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Assign a Role (Please Note! This user will not be able to log in via SU SSO):</strong>
                <select name="roles[]" class="form-control" multiple="multiple">
                    @foreach($roles as $role)
                        <option value="{{$role}}">{{$role}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
    </form>


@endsection
