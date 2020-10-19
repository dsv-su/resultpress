@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Assign Project</h2>
            </div>
            <br>
            <div class="pull-right">
                <a class="btn btn-outline-primary" href="{{ route('admin') }}"> Back</a>
            </div>
            <br>
        </div>
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table class="table table-bordered">
        <tr>
            <th>Id</th>
            <th>Project</th>
            <th>Start</th>
            <th>End</th>
            <th width="200px">Assign to:</th>
            <th>Action</th>
        </tr>
        @foreach ($data as $key => $project)
            <tr>
                <td>{{ $project->id }}</td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->start }}</td>
                <td>{{ $project->end }}</td>
                <td>
                    @foreach($project->project_owner->all() as $owner)
                    {{ $owner->user->name }}
                    @endforeach
                </td>
                <td>
                    <a class="btn btn-outline-primary" href="{{ route('projectadmin.edit',$project->id) }}">Edit</a>
                    <a class="btn btn-outline-success float-right" href="{{route('invite_view', $project)}}">Invite</a>
                </td>
            </tr>
        @endforeach
    </table>
@endsection
