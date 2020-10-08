@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Change Project Leader</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('projectadmin.index') }}"> Back</a>
            </div>
            <br>
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
    <table class="table table-bordered">
        <tr>
            <th>Id</th>
            <th>Project Name</th>
            <th>Assigned to:</th>
            <th>Change:</th>
        </tr>
        <tr>
            <td>{{$project->id}}</td>
            <td>{{ old('name', empty($project) ? '' : $project->name) }}</td>
            <td>
                <form action="{{ route('projectadmin.update', $project->id) }}" method="POST">
                    @method('PATCH')
                    @csrf
                <select name="user_id" class="form-control">
                    <option value="{{$project->user_id}}" selected>{{$project->user->name}}</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
            </td>
            <td><button type="submit" class="btn btn-primary">Submit</button></td>
            </form>
        </tr>



@endsection
