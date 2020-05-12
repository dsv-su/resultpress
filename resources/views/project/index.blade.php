@extends('layouts.master')

@section('content')
    <table id="example" class="table table-sm table-striped table-bordered" style="width:100%" data-order='[[ 0, "desc" ]]' data-page-length='25'>
        <thead>
        <tr>
            <th>Id</th>
            <th>Project Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach( $projects as $project )
            <tr>
                <td>{{ $project->id }}</td>
                <td><a href="/project/{{$project->id}}">{{ $project->name}}</a></td>
                <td>
                    @if($project->status == 1) <span class="badge badge-warning">In progress</span>
                    @elseif($project->status == 2) <span class="badge badge-success">Done</span>
                    @elseif($project->status == 2) <span class="badge badge-danger">Delayed</span>
                    @endif
                </td>
                <td class="text-center">
                    <a href="/project/{{$project->id}}" class="btn btn-outline-success btn-sm"><i class="fas fa-eye"></i></a>
                    <a href="/project/{{$project->id}}/edit" class="btn btn-outline-primary btn-sm"><i class="far fa-edit"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
