@extends('layouts.master')

@section('content')
<h4>Project updates</h4>
<p>Project: {{$project->name}}</p>
@if($project_updates)
<table>
    <thead>
        <th>#</th>
        <th>Summary</th>
        <th>Created at</th>
        <th>Action</th>
    </thead>

@foreach ($project_updates as $pu)
<tr>
    <td>{{$pu->id}}</td>
    <td>{{$pu->summary}}</td>
    <td>{{$pu->created_at}}</td>
    <td><a href="/project/update/{{$pu->id}}" class="btn btn-outline-primary btn-sm"><i class="fas fa-info-circle"></i></a></td>
</tr>
@endforeach
</table>
@endif

@endsection
