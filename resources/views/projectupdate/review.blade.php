@extends('layouts.master')

@section('content')
<h4>Update #{{$project_update->id}}</h4>

<h5>Update details:</h5>
<p>Date: {{$project_update->created_at}}</p>
<p>Summary: {{$project_update->summary}}</p>

@if(!$activity_updates->isEmpty())
<h5>Activities covered:</h5>
<table class="table table-sm table-striped table-bordered" style="width:100%" id="activities_table">
    <thead>
        <th>Activity</th>
        <th>Status</th>
        <th>Comment</th>
        <th>Money spent</th>
        <th>Date(s)</th>
    </thead>
    @foreach($activity_updates as $au)
    <tr>
        <td>{{$au->title}}</td>
        @if($au->status == 1)<td class="status inprogress">Started</td>
        @elseif($au->status == 2)<td class="status delayed">Delayed</td>
        @elseif($au->status == 3)<td class="status done">Done</td>
        @endif
        <td>{{$au->comment}}</td>
        <td>{{$au->money}}</td>
        <td>{{$au->date->format('d/m/Y')}}</td>
    </tr>
    @endforeach
</table>
@endif

@if(!$output_updates->isEmpty())
<h5>Outputs covered:</h5>
<table class="table table-sm table-striped table-bordered" style="width:100%" id="activities_table">
    <thead>
        <th>Output</th>
        <th>Value</th>
    </thead>
    @foreach($output_updates as $ou)
    <tr>
        <td>{{$ou->indicator}}</td>
        <td>{{$ou->value}}</td>
    </tr>
    @endforeach
</table>
@endif

@endsection
