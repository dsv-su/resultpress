@extends('layouts.master')

@section('content')
    <h4>{{ $project->name }}: project updates</h4>
    <p><a href="{{ route('project_show', $project->id) }}">Back to project page</a></p>
    @if($project->projectupdate)
        <table>
            <thead>
            <th>#</th>
            <th>Summary</th>
            <th>Submitted at</th>
            <th>Actions</th>
            </thead>

            @foreach ($project->projectupdate as $index => $pu)
                <tr>
                    <td class="fit">{{$index+1}}</td>
                    <td class="w-75">@if($pu->summary){{$pu->summary}} @else No summary provided @endif</td>
                    <td class="fit">{{$pu->created_at->format('d/m/Y')}}</td>
                    <td class="text-nowrap" ><a href="/project/update/{{$pu->id}}" class="btn btn-outline-secondary btn-sm">Show <i
                                    class="fas fa-info-circle"></i></a>
                        <a href="/project/update/{{$pu->id}}/review" class="btn btn-outline-secondary btn-sm">Review <i
                                    class="fas fa-highlighter"></i></a>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif

@endsection