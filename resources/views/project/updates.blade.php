@extends('layouts.master')

@section('content')
<h4>Project updates</h4>
<p>Project: {{$project->name}}</p>
<p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>
@if($project->projectupdate)
<table>
    <thead>
        <th>#</th>
        <th>Summary</th>
        <th>Created at</th>
        <th>Action</th>
    </thead>

    @foreach ($project->projectupdate as $index => $pu)
    <tr>
        <td>{{$index+1}}</td>
        <td>@if($pu->summary){{$pu->summary}} @else No summary provided @endif</td>
        <td>{{$pu->created_at->format('d/m/Y')}}</td>
        <td><a href="/project/update/{{$pu->id}}" class="btn btn-outline-primary btn-sm"><i
                    class="fas fa-info-circle"></i></a>
            <a href="/project/update/{{$pu->id}}/review" class="btn btn-outline-primary btn-sm"><i
                    class="fas fa-highlighter"></i></a>
        </td>
    </tr>
    @endforeach
</table>
@endif

@endsection