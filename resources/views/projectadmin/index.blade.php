@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Manage Project</h2>
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
    <table id="projectsTable" class="table table-bordered" data-order='[[ 0, "desc" ]]' data-page-length='10'>
        <thead>
        <tr>
            <th>Id</th>
            <th>Project</th>
            <th>Start</th>
            <th>End</th>
            <th>Assign to:</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $key => $project)
            <tr>
                <td>{{ $project->id }}</td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->start->format('d-m-Y') }}</td>
                <td>{{ $project->end->format('d-m-Y') }}</td>
                <td>
                    @foreach($project->project_owner->all() as $owner)
                        {{ $owner->user->name }}
                    @endforeach
                </td>
                <td>
                    @can("project-$project->id-edit")
                    <a class="btn btn-outline-primary" href="{{ route('projectadmin.edit',$project->id) }}">Edit</a>
                    <a class="btn btn-outline-success float-right" href="{{route('invite_view', $project)}}">Invite</a>
                    @endcan
                </td>
            </tr>
        @endforeach
        </tbody>

    </table>
    <script>
        $(document).ready( function () {
            $('#projectsTable').DataTable();
        } );
    </script>
    </script>
@endsection
