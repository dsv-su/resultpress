@extends('layouts.master')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('project_show', $project->id) }}">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">History</li>
        </ol>
    </nav>
    <div class="row justify-content-between">
        <div class="col-6"></div>
        <div class="col-sm-auto d-flex align-items-center">
            @if($project->status() == 'planned')
                <span class="badge badge-light font-100">Planned</span>
            @elseif($project->status() == 'inprogress')
                <span class="badge badge-warning font-100">In progress</span>
            @elseif($project->status() == 'delayedhigh')
                <span class="badge badge-danger font-100">Delayed Major</span>
            @elseif($project->status() == 'delayednormal')
                <span class="badge badge-danger font-100">Delayed</span>
            @elseif($project->status() == 'pendingreview')
                <span class="badge badge-primary font-100">Pending review</span>
            @elseif($project->status() == 'completed')
                <span class="badge badge-success font-100">Completed</span>
            @elseif($project->status() == 'archived')
                <span class="badge badge-secondary font-100">Archived</span>
            @elseif($project->status() == 'onhold')
                <span class="badge badge-secondary font-100">On hold</span>
            @elseif($project->status() == 'terminated')
                <span class="badge badge-secondary font-100">Closed</span>
            @endif
        </div>
    </div>

    <p>@include('project.action_links')</p>

    @foreach($history as $i => $data)
        <div class="card my-2 bg-white">
            <div class="card-header">
                #{{$i+1}} from {{$data['created']}} by {{$data['user']}}
            </div>
            <div class="card-body">
                @if (isset($data['modified']))
                    @if (!is_array($data['modified']))
                        {{$data['modified']}}
                    @else
                        <h4>Modified:</h4>
                        @foreach($data['modified'] as $k => $m)
                            <h5 class="mt-2">{{ucfirst(str_replace('_', ' ', $k))}}</h5>
                            @foreach($m as $j => $value)
                                @if ($k == 'project_updates')
                                    <p><a href="/project/update/{{$j}}">#{{$j}}</a>: {{json_encode($value)}}</p>
                                @else
                                    <p>@if ($k != 'project_owner' && $k != 'partners' && $k != 'areas') {{$j}}: @endif {{json_encode($value)}}</p>
                                @endif
                            @endforeach
                        @endforeach
                    @endif
                @endif
                @if (isset($data['added']))
                    <h4>Added:</h4>
                    @foreach($data['added'] as $k => $a)
                        <h5 class="mt-2">{{ucfirst(str_replace('_', ' ', $k))}}</h5>
                        @foreach($a as $j => $value)
                            @if ($k == 'project_updates')
                                <p><a href="/project/update/{{$j}}">#{{$j}}</a>: {{json_encode($value)}}</p>
                            @else
                                <p>{{json_encode($value)}}</p>
                            @endif
                        @endforeach
                    @endforeach
                @endif
                @if (isset($data['removed']))
                    <h4>Removed:</h4>
                    @foreach($data['removed'] as $k => $r)
                        <h5 class="mt-2">{{ucfirst(str_replace('_', ' ', $k))}}</h5>
                        @foreach($r as $j => $value)
                            @if ($k == 'project_updates')
                                <p><a href="/project/update/{{$j}}">{{$j}}</a>: {{json_encode($value)}}</p>
                            @else
                                <p>{{json_encode($value)}}</p>
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach

@endsection