@extends('layouts.master')

@section('content')
<table id="example" class="table table-sm table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Id</th>
            <th>Project Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $project->id }}</td>
            <td>{{ $project->name}}</td>
            <td>
                @if($project->status == 1) <span class="badge badge-warning">In progress</span>
                @elseif($project->status == 2) <span class="badge badge-success">Done</span>
                @elseif($project->status == 2) <span class="badge badge-danger">Delayed</span>
                @endif
            </td>
            <td class="text-center">
                <a href="/project/{{$project->id}}/edit" class="btn btn-outline-primary btn-sm"><i
                        class="far fa-edit"></i></a>
                <a class="btn btn-danger btn-sm" href=""><i class="far fa-trash-alt"></i></a>
            </td>
        </tr>
        <tr>
            <td colspan="4">{{ $project->description }}</td>
        </tr>
    </tbody>
</table>
@endsection
