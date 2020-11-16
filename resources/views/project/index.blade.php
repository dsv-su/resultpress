@extends('layouts.master')

@section('content')
    <table id="example" class="table table-sm table-striped table-bordered" style="width:100%"
           data-order='[[ 0, "desc" ]]'
           data-page-length='25'>
        <thead>
        <tr>
            <th>Id</th>
            <th>Project Name</th>
            <th>Project Area</th>
            <th>Status</th>
            <th>Manager</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $projects as $project )
            <tr>
                <td>{{ $project->id }}</td>
                <td><a href="/project/{{$project->id}}">{{ $project->name}}</a></td>
                <td>@if($project->project_area){{$project->project_area->name}}@endif</td>
                <td>
                    @if($project->status == 1) <span class="badge badge-warning">In progress</span>
                    @elseif($project->status == 2) <span class="badge badge-danger">Delayed</span>
                    @elseif($project->status == 3) <span class="badge badge-success">Done</span>
                    @endif
                    @if($project->pending_updates()->count())
                        <a href="/project/{{$project->id}}/updates"><span class=" badge badge-info">Update
                        pending</span></a>
                    @endif
                </td>
                <td>
                    @foreach($project->project_owner->all() as $project_owner)
                        {{$project_owner->user->name}}
                    @endforeach
                </td>
                <td class="text-center mw-400">
                    @include('project.action_links')
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
