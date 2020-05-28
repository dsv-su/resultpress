@extends('layouts.master')

@section('content')
<h4>Project details</h4>
<table class="table table-sm table-striped table-bordered" style="width:100%">
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

@if(!$activities->isEmpty())
<h4>Activities</h4>
<table class="table table-sm table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Activity name</th>
            <th>Start</th>
            <th>End</th>
            <th>Budget</th>
        </tr>
    </thead>
    @foreach ($activities as $activity)
    <tr>
        <td>{{$activity->title}}</td>
        <td>{{$activity->start->format('d/m/Y')}}</td>
        <td>{{$activity->end->format('d/m/Y')}}</td>
        <td>{{$activity->budget}}</td>
    </tr>
    @if ($activity->description)
    <tr>
        <td colspan="4">{{$activity->description}}</td>
    </tr>
    @endif
    @endforeach
    @endif
</table>

@if(!$outputs->isEmpty())
<h4>Outputs</h4>
<table class="table table-sm table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Indicator</th>
            <th>Target</th>
        </tr>
    </thead>
    <!-- Here comes a foreach to show the outputs -->
    @foreach ($outputs as $output)
    <tr>
        <td>{{$output->indicator}}</td>
        <td>{{$output->target}}</td>
    </tr>
    @endforeach
    @endif
</table>

@endsection
