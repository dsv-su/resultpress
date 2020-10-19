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
            <p>This transfers all <strong>Roles and Permissions</strong> from one user to another.</p>
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
            @foreach($project->project_owner->all() as $key => $owner)
                <th>{{$key+1}}). Assigned to:</th>
            @endforeach
            <th>Change:</th>
        </tr>
        <tr>
            <td>{{$project->id}}</td>
            <td>{{ old('name', empty($project) ? '' : $project->name) }}</td>
            <form action="{{ route('projectadmin.update', $project->id) }}" method="POST">

                @method('PATCH')
                @csrf
                @if(count($project->project_owner) > 1)
                    @foreach($project->project_owner->all() as $owner)
                        <td>
                            <input type="text" name="old_user_id[]" value="{{$owner->user->id}}" hidden>
                            <select name="user_id[]" class="form-control">
                                <option value="{{$owner->user->id}}" selected>{{$owner->user->name}}</option>

                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </td>
                        @endforeach
                        </select>
                        @else
                            <td>
                                <input type="text" name="old_user_id" value="{{$project->project_owner->first()->user->id}}" hidden>
                                <select name="user_id" class="form-control">
                                    <option value="{{$project->project_owner->first()->user->id}}" selected>{{$project->project_owner->first()->user->name}}</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif

                        <td>
                            <button type="submit" class="btn btn-outline-primary">Submit</button>
                            <button type="button" name="add-user" class="btn btn-outline-primary add-user">Add</button>
                        </td>
            </form>
        </tr>
        </tr>
    </table>
    <table class="table table-sm" id="users_table" style="display:none;">
        <form action="{{ route('projectadmin.store', $project->id) }}" method="POST">
            @csrf
        <thead>
        <th scope="row">Add user:</th>
        <th scope="row">Confirm:</th>
        </thead>
        <tbody>
        <td>
            <input type="text" name="project_id" value="{{$project->id}}" hidden>
        <select name="add_user_id" class="form-control">
        @foreach($users as $user)
                <option value="{{$user->id}}">[{{$user->id}}]  {{$user->name}} ({{$user->email}})</option>
        @endforeach
        </select>
        </td>
        <td>
            <button type="submit" class="btn btn-outline-primary">Add this user</button>

        </td>
        </tbody>
        </form>
        <div class="card">
            <h5 class="alert alert-primary card-header">Notification</h5>
            <div class="card-body">
                <h5 class="card-title">Under development</h5>
                <p class="card-text">This form is under development</p>
            </div>
        </div>
    </table>
        <script>
            $(document).ready(function () {
                $(document).on('click', '.add-user', function () {
                    $('#users_table').show();
                });
            });
        </script>


@endsection
