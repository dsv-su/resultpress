@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Role</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('roles.index') }}"> Back</a>
            </div>
        </div>
    </div>
    @if (count($errors) > 0)
        <br>
        <div class="alert alert-danger">
            There are some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @method('PATCH')
        @csrf
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input class="form-control" name="name" type="text" placeholder="Name" value="{{ old('name', empty($role) ? '' : $role->name) }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong>Basic permission:</strong>
                    <br /><br />
                    @foreach ($permission->filter(function($p) { return preg_match('/project-[0-9]+-.*/', $p->name) !== 1; }) as $value)
                        <label>
                            <input type="checkbox" name="permission[]" value={{ $value->id }} @if ($role->hasPermissionTo($value->name)) checked @endif class="name">
                            {{ $value->name }}</label>
                        <br />
                    @endforeach
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 mt-4">
                <div class="form-group">
                    <strong><a href="#" class="projects-permissions">Projects permissions</a></strong>
                    <br /><br />
                    @foreach ($permission->filter(function($p) { return preg_match('/project-[0-9]+-.*/', $p->name) === 1; }) as $value)
                        <div class="project-permission hidden">
                            <label>
                                <input type="checkbox" name="permission[]" value={{ $value->id }} @if ($role->hasPermissionTo($value->name)) checked @endif class="name">
                                {{ $value->name }}
                            </label>
                            <br />
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection

